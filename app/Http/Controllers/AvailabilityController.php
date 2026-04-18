<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * AvailabilityController
 *
 * Manages the weekly availability grid for volunteers. Availability is stored
 * as individual hour-slots in the `availability` table — one row per
 * (volunteer, week_of_month, day_of_week, hour_start) combination.
 *
 * The grid supports 4 weeks × 7 days × 24 hours, giving coordinators a
 * detailed picture of when each volunteer is free across a typical month.
 *
 * All writes use a delete-and-reinsert pattern within a transaction rather
 * than diffing individual slots. This keeps the logic simple and fast;
 * the bulk insert in `buildSlotRows()` means even a fully populated grid
 * (up to 672 slots) writes in a single query.
 */
class AvailabilityController extends Controller
{
    /**
     * Display the availability grid for a volunteer.
     *
     * Slots are keyed by "week-day-hour" string (e.g. "1-3-9") so the Blade
     * template can do a fast O(1) lookup per cell rather than scanning the
     * collection for each of the 672 possible cells.
     */
    public function show(Volunteer $volunteer)
    {
        $this->authorizeVolunteerOrCoordinator($volunteer);

        $slots = $volunteer->availability()
            ->where('is_available', true)
            ->get()
            ->keyBy(fn($s) => "{$s->week_of_month}-{$s->day_of_week}-{$s->hour_start}");

        return view('volunteer.availability', compact('volunteer', 'slots'));
    }

    /**
     * Replace a volunteer's availability with the submitted grid.
     *
     * The entire slot set is rebuilt on every save (delete-then-insert) so
     * partial updates and full clears both work with the same code path. The
     * operation runs inside a transaction so a failure mid-insert never leaves
     * the volunteer with a partially saved grid.
     *
     * The request body is expected in the shape:
     *   availability[week][day][hour] = "1"
     *
     * Supports both JSON (fetch) and standard form POST responses.
     */
    public function update(Request $request, Volunteer $volunteer)
    {
        $this->authorizeVolunteerOrCoordinator($volunteer);

        $data = $request->input('availability', []);

        return DB::transaction(function () use ($data, $volunteer, $request) {
            // Wipe existing slots before rebuilding from the submitted grid.
            $volunteer->availability()->delete();

            $toInsert = $this->buildSlotRows($volunteer->volunteer_id, $data);

            if (!empty($toInsert)) {
                // Single bulk insert is significantly faster than individual
                // Eloquent creates, especially for fully populated grids.
                DB::table('availability')->insert($toInsert);
            }

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'update_volunteer',
                'entity_type'    => 'availability',
                'entity_id'      => $volunteer->volunteer_id,
                'change_details' => ['slots_updated' => true],
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Availability saved.']);
            }

            return redirect()->route('availability.show', $volunteer)
                ->with('success', 'Availability updated successfully.');
        });
    }

    /**
     * Return a volunteer's availability in a format suited for the matching engine.
     *
     * Used by the coordinator matching UI to determine which volunteers are free
     * on the days/times a meeting slot requires. Returns a flat list of slot
     * objects alongside a convenience `is_available` boolean.
     */
    public function getForMatching(Volunteer $volunteer)
    {
        $slots     = $volunteer->availability()->where('is_available', true)->get();
        $dayLabels = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];

        $formatted = $slots->map(fn($s) => [
            'week'      => $s->week_of_month,
            'day'       => $dayLabels[$s->day_of_week] ?? 'Unknown',
            'day_index' => $s->day_of_week,
            'hour'      => $s->hour_start,
        ]);

        return response()->json([
            'volunteer_id'          => $volunteer->volunteer_id,
            'is_available'          => $formatted->isNotEmpty(),
            'available_slots'       => $formatted,
            'total_available_slots' => $formatted->count(),
        ]);
    }

    /**
     * Apply the same availability grid to multiple volunteers at once.
     *
     * Admin-only. Useful when a group of new volunteers all share the same
     * schedule. Uses the same bulk-insert approach as `update()` for efficiency.
     */
    public function bulkUpdate(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'volunteer_ids'   => 'required|array',
            'volunteer_ids.*' => 'exists:volunteers,volunteer_id',
            'availability'    => 'required|array',
        ]);

        return DB::transaction(function () use ($validated) {
            $updatedCount = 0;

            foreach ($validated['volunteer_ids'] as $volunteerId) {
                $volunteer = Volunteer::find($volunteerId);
                if (!$volunteer) continue;

                $volunteer->availability()->delete();

                $toInsert = $this->buildSlotRows($volunteer->volunteer_id, $validated['availability']);

                if (!empty($toInsert)) {
                    DB::table('availability')->insert($toInsert);
                }

                AuditLog::create([
                    'actor_user_id'  => auth()->id(),
                    'action'         => 'update_volunteer',
                    'entity_type'    => 'availability',
                    'entity_id'      => $volunteer->volunteer_id,
                    'change_details' => ['bulk_update' => true],
                ]);

                $updatedCount++;
            }

            return redirect()->back()
                ->with('success', "{$updatedCount} volunteer(s) availability updated.");
        });
    }

    /**
     * Convert the nested availability input array into a flat list of DB rows.
     *
     * Input shape: $data[week][day][hour] = "1"
     * Only cells with value "1" become rows — unchecked cells are simply absent.
     * Each row gets a fresh ULID so the bulk insert satisfies the primary key
     * constraint without needing Eloquent's auto-generation.
     *
     * @param  string $volunteerId  The ULID of the volunteer being updated.
     * @param  array  $data         The nested availability grid from the request.
     * @return array  Flat array of associative row arrays ready for DB::insert().
     */
    private function buildSlotRows(string $volunteerId, array $data): array
    {
        $rows = [];
        $now  = now();

        foreach ($data as $week => $days) {
            foreach ($days as $day => $hours) {
                foreach ($hours as $hour => $value) {
                    if ($value == '1') {
                        $rows[] = [
                            'availability_id' => (string) Str::ulid(),
                            'volunteer_id'    => $volunteerId,
                            'week_of_month'   => (int) $week,
                            'day_of_week'     => (int) $day,
                            'hour_start'      => (int) $hour,
                            'is_available'    => true,
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * Authorize access to a specific volunteer's availability.
     *
     * A volunteer may view and edit only their own availability (matched by
     * email). Coordinators and admins may access any volunteer's grid.
     */
    private function authorizeVolunteerOrCoordinator(Volunteer $volunteer): void
    {
        $user = auth()->user();

        // Let the volunteer through immediately if it's their own record.
        if ($user->email === $volunteer->email) {
            return;
        }

        // Otherwise require coordinator or admin role.
        if (!$user->hasAnyRole(['coordinator', 'admin'])) {
            abort(403);
        }
    }
}
