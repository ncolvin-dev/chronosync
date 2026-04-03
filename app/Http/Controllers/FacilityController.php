<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Models\Facility;
use App\Models\AuditLog;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    /**
     * List facilities.
     */
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = Facility::withoutTrashed();

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by type
        if ($request->filled('facility_type')) {
            $query->where('facility_type', $request->facility_type);
        }

        // Search by name
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('name', 'like', $search)
                  ->orWhere('address_city', 'like', $search);
        }

        $facilities = $query->orderBy('facility_name')->paginate(15);

        return view('coordinator.facilities', compact('facilities'));
    }

    /**
     * Show facility creation form.
     */
    public function create()
    {
        $this->authorizeCoordinatorOrAdmin();

        return view('placeholder.coming-soon');
    }

    /**
     * Store facility.
     */
    public function store(StoreFacilityRequest $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            $facility = Facility::create([
                'name' => $validated['name'],
                'facility_type' => $validated['facility_type'],
                'description' => $validated['description'] ?? null,
                'address_street' => $validated['address_street'],
                'address_city' => $validated['address_city'],
                'address_state' => $validated['address_state'],
                'address_zip' => $validated['address_zip'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'contact_person' => $validated['contact_person'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Store hours if provided
            $hoursData = [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                if ($request->filled("hours_{$day}_open") && $request->filled("hours_{$day}_close")) {
                    $hoursData[$day] = [
                        'open' => $validated["hours_{$day}_open"],
                        'close' => $validated["hours_{$day}_close"],
                    ];
                }
            }

            if (!empty($hoursData)) {
                $facility->hours = json_encode($hoursData);
                $facility->save();
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'facility_created',
                'model_type' => Facility::class,
                'model_id' => $facility->id,
                'changes' => ['created' => $facility->toArray()],
            ]);

            return redirect()->route('facilities.show', $facility)
                ->with('success', 'Facility created successfully.');
        });
    }

    /**
     * Show facility detail.
     */
    public function show(Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meetings = $facility->meetings()
            ->where('date_scheduled', '>=', now())
            ->orderBy('date_scheduled')
            ->paginate(10);

        return view('placeholder.coming-soon', compact('facility', 'meetings'));
    }

    /**
     * Show facility edit form.
     */
    public function edit(Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        return view('placeholder.coming-soon', compact('facility'));
    }

    /**
     * Update facility.
     */
    public function update(StoreFacilityRequest $request, Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $facility, $request) {
            $changes = [];

            $updateFields = [
                'name',
                'facility_type',
                'description',
                'address_street',
                'address_city',
                'address_state',
                'address_zip',
                'phone',
                'email',
                'contact_person',
                'capacity',
                'notes',
                'is_active',
            ];

            foreach ($updateFields as $field) {
                if (isset($validated[$field]) && $facility->$field !== $validated[$field]) {
                    $changes[$field] = [
                        'old' => $facility->$field,
                        'new' => $validated[$field],
                    ];
                    $facility->$field = $validated[$field];
                }
            }

            // Update hours
            $hoursData = [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                if ($request->filled("hours_{$day}_open") && $request->filled("hours_{$day}_close")) {
                    $hoursData[$day] = [
                        'open' => $validated["hours_{$day}_open"],
                        'close' => $validated["hours_{$day}_close"],
                    ];
                }
            }

            $newHours = !empty($hoursData) ? json_encode($hoursData) : null;
            if ($facility->hours !== $newHours) {
                $changes['hours'] = [
                    'old' => $facility->hours,
                    'new' => $newHours,
                ];
                $facility->hours = $newHours;
            }

            $facility->save();

            if (!empty($changes)) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'facility_updated',
                    'model_type' => Facility::class,
                    'model_id' => $facility->id,
                    'changes' => $changes,
                ]);
            }

            return redirect()->route('facilities.show', $facility)
                ->with('success', 'Facility updated successfully.');
        });
    }

    /**
     * Toggle facility active/inactive status.
     */
    public function toggleStatus(Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        $oldStatus = $facility->is_active;
        $facility->is_active = !$oldStatus;
        $facility->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'facility_status_changed',
            'model_type' => Facility::class,
            'model_id' => $facility->id,
            'changes' => [
                'is_active' => [
                    'old' => $oldStatus,
                    'new' => !$oldStatus,
                ],
            ],
        ]);

        $statusText = $facility->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Facility {$statusText} successfully.");
    }

    /**
     * Soft delete facility and cascade.
     */
    public function destroy(Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        return DB::transaction(function () use ($facility) {
            // Soft delete associated meetings
            Meeting::where('facility_id', $facility->id)->delete();

            // Soft delete facility
            $facility->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'facility_deleted',
                'model_type' => Facility::class,
                'model_id' => $facility->id,
                'changes' => ['deleted' => true],
            ]);

            return redirect()->route('facilities.index')
                ->with('success', 'Facility and associated meetings deleted successfully.');
        });
    }

    /**
     * Authorize coordinator or admin access.
     */
    private function authorizeCoordinatorOrAdmin()
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('coordinator', $roles) && !in_array('admin', $roles)) {
            abort(403);
        }
    }
}
