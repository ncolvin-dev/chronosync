<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvailabilityController extends Controller
{
    /**
     * Get volunteer availability grid (35 slots: 5 weeks × 7 days).
     */
    public function show(Volunteer $volunteer)
    {
        $this->authorizeVolunteerOrCoordinator($volunteer);

        $availability = json_decode($volunteer->availability, true) ?? array_fill(0, 35, false);

        // Structure as 5 weeks × 7 days
        $grid = [];
        $dayLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        for ($week = 0; $week < 5; $week++) {
            $grid[$week] = [];
            for ($day = 0; $day < 7; $day++) {
                $index = ($week * 7) + $day;
                $grid[$week][$day] = [
                    'day_label' => $dayLabels[$day],
                    'available' => $availability[$index] ?? false,
                    'slot_index' => $index,
                ];
            }
        }

        return view('volunteer.availability', compact('volunteer', 'grid'));
    }

    /**
     * Update volunteer availability pattern.
     */
    public function update(Request $request, Volunteer $volunteer)
    {
        $this->authorizeVolunteerOrCoordinator($volunteer);

        $validated = $request->validate([
            'availability' => 'required|array|size:35',
            'availability.*' => 'boolean',
        ]);

        return DB::transaction(function () use ($validated, $volunteer) {
            $oldAvailability = json_decode($volunteer->availability, true) ?? array_fill(0, 35, false);
            $newAvailability = array_values($validated['availability']);

            // Check if there are changes
            $hasChanges = false;
            for ($i = 0; $i < 35; $i++) {
                if (($oldAvailability[$i] ?? false) !== ($newAvailability[$i] ?? false)) {
                    $hasChanges = true;
                    break;
                }
            }

            if ($hasChanges) {
                $volunteer->availability = json_encode($newAvailability);
                $volunteer->save();

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'volunteer_availability_updated',
                    'model_type' => Volunteer::class,
                    'model_id' => $volunteer->id,
                    'changes' => [
                        'availability' => [
                            'old' => $oldAvailability,
                            'new' => $newAvailability,
                        ],
                    ],
                ]);
            }

            return redirect()->route('availability.show', $volunteer)
                ->with('success', 'Availability updated successfully.');
        });
    }

    /**
     * Get optimized availability for matching engine.
     */
    public function getForMatching(Volunteer $volunteer)
    {
        // This endpoint is typically called by the matching service
        // Return availability in a format optimized for the matching algorithm

        $availability = json_decode($volunteer->availability, true) ?? array_fill(0, 35, false);

        // Create a more usable format with day/week info
        $formatted = [];
        $dayLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        for ($week = 0; $week < 5; $week++) {
            for ($day = 0; $day < 7; $day++) {
                $index = ($week * 7) + $day;
                if ($availability[$index] ?? false) {
                    $formatted[] = [
                        'week' => $week + 1,
                        'day' => $dayLabels[$day],
                        'day_index' => $day,
                        'slot_index' => $index,
                    ];
                }
            }
        }

        return response()->json([
            'volunteer_id' => $volunteer->id,
            'is_available' => !empty($formatted),
            'available_slots' => $formatted,
            'total_available_slots' => count($formatted),
        ]);
    }

    /**
     * Bulk update availability for multiple volunteers (admin only).
     */
    public function bulkUpdate(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'volunteer_ids' => 'required|array',
            'volunteer_ids.*' => 'exists:volunteers,id',
            'availability' => 'required|array|size:35',
            'availability.*' => 'boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            $newAvailability = json_encode(array_values($validated['availability']));
            $updatedCount = 0;

            foreach ($validated['volunteer_ids'] as $volunteerId) {
                $volunteer = Volunteer::find($volunteerId);

                if ($volunteer->availability !== $newAvailability) {
                    $oldAvailability = $volunteer->availability;
                    $volunteer->availability = $newAvailability;
                    $volunteer->save();

                    AuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'volunteer_availability_bulk_updated',
                        'model_type' => Volunteer::class,
                        'model_id' => $volunteer->id,
                        'changes' => [
                            'bulk_update' => true,
                            'old_availability' => $oldAvailability,
                            'new_availability' => $newAvailability,
                        ],
                    ]);

                    $updatedCount++;
                }
            }

            return redirect()->back()
                ->with('success', "{$updatedCount} volunteer(s) availability updated.");
        });
    }

    /**
     * Authorize volunteer viewing own or coordinator viewing any.
     */
    private function authorizeVolunteerOrCoordinator(Volunteer $volunteer)
    {
        $user = auth()->user();

        // Own profile
        if ($user->id === $volunteer->user_id) {
            return;
        }

        // Coordinator or admin
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('coordinator', $roles) && !in_array('admin', $roles)) {
            abort(403);
        }
    }

    /**
     * Authorize admin only.
     */
    private function authorizeAdmin()
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('admin', $roles)) {
            abort(403);
        }
    }
}
