<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Volunteer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvailabilityController extends Controller
{
    public function show(Volunteer $volunteer)
    {
        $this->authorizeVolunteerOrCoordinator($volunteer);

        // Build a fast lookup keyed by "week-day-hour"
        $slots = $volunteer->availability()
            ->where('is_available', true)
            ->get()
            ->keyBy(fn($s) => "{$s->week_of_month}-{$s->day_of_week}-{$s->hour_start}");

        return view('volunteer.availability', compact('volunteer', 'slots'));
    }

    public function update(Request $request, Volunteer $volunteer)
    {
        $this->authorizeVolunteerOrCoordinator($volunteer);

        $data = $request->input('availability', []);

        return DB::transaction(function () use ($data, $volunteer, $request) {
            // Wipe existing slots and rebuild from submitted data
            $volunteer->availability()->delete();

            foreach ($data as $week => $days) {
                foreach ($days as $day => $hours) {
                    foreach ($hours as $hour => $value) {
                        if ($value == '1') {
                            Availability::create([
                                'volunteer_id'  => $volunteer->volunteer_id,
                                'week_of_month' => (int) $week,
                                'day_of_week'   => (int) $day,
                                'hour_start'    => (int) $hour,
                                'is_available'  => true,
                            ]);
                        }
                    }
                }
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

    public function getForMatching(Volunteer $volunteer)
    {
        $slots = $volunteer->availability()->where('is_available', true)->get();
        $dayLabels = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];

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

                foreach ($validated['availability'] as $week => $days) {
                    foreach ($days as $day => $hours) {
                        foreach ($hours as $hour => $value) {
                            if ($value == '1') {
                                Availability::create([
                                    'volunteer_id'  => $volunteer->volunteer_id,
                                    'week_of_month' => (int) $week,
                                    'day_of_week'   => (int) $day,
                                    'hour_start'    => (int) $hour,
                                    'is_available'  => true,
                                ]);
                            }
                        }
                    }
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

    private function authorizeVolunteerOrCoordinator(Volunteer $volunteer)
    {
        $user = auth()->user();

        if ($user->email === $volunteer->email) {
            return;
        }

        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('coordinator', $roles) && !in_array('admin', $roles)) {
            abort(403);
        }
    }

    private function authorizeAdmin()
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('admin', $roles)) {
            abort(403);
        }
    }
}
