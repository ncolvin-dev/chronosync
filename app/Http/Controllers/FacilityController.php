<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Meeting;
use App\Models\AuditLog;
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

        // Filter by status ('active' / 'inactive')
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name, city, or address
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'ilike', $search)
                  ->orWhere('city', 'ilike', $search)
                  ->orWhere('address', 'ilike', $search);
            });
        }

        $facilities = $query->orderBy('facility_name')->paginate(15);

        return view('coordinator.facilities', compact('facilities'));
    }

    /**
     * Show facility creation form (currently coming-soon placeholder).
     */
    public function create()
    {
        $this->authorizeCoordinatorOrAdmin();
        return view('placeholder.coming-soon');
    }

    /**
     * Store a new facility and its initial recurring meeting slots.
     *
     * Expects optional meetings[] array in the request, each element being:
     *   day_of_week, week_of_month, meeting_time, duration_minutes, format, volunteers_needed
     */
    public function store(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            // Facility fields
            'facility_name'          => 'required|string|max:255',
            'address'                => 'required|string|max:255',
            'city'                   => 'required|string|max:100',
            'state'                  => 'required|string|size:2',
            'zip'                    => 'required|string|max:10',
            'main_phone'             => 'required|string|max:20',
            'contact_email'          => 'nullable|email|max:255',
            'clean_time_requirement' => 'required|integer|min:0',
            'credentialing_types'    => 'nullable|array',
            'credentialing_types.*'  => 'string',
            'gender_restriction'     => 'nullable|boolean',
            'probation_allowed'      => 'nullable|boolean',
            'timezone'               => 'nullable|string|max:100',
            'contact1_name'          => 'nullable|string|max:255',
            'contact1_phone'         => 'nullable|string|max:20',
            'contact1_email'         => 'nullable|email|max:255',
            'contact2_name'          => 'nullable|string|max:255',
            'contact2_phone'         => 'nullable|string|max:20',
            'contact2_email'         => 'nullable|email|max:255',

            // Meeting slots (zero or more)
            'meetings'                         => 'nullable|array|max:20',
            'meetings.*.day_of_week'           => 'required_with:meetings|integer|between:0,6',
            'meetings.*.week_of_month'         => 'required_with:meetings|integer|between:1,5',
            'meetings.*.meeting_time'          => 'required_with:meetings|date_format:H:i',
            'meetings.*.duration_minutes'      => 'nullable|integer|min:15|max:480',
            'meetings.*.format'                => 'required_with:meetings|in:in_person,virtual,hybrid',
            'meetings.*.volunteers_needed'     => 'required_with:meetings|integer|between:1,5',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $facility = Facility::create([
                'facility_name'          => $validated['facility_name'],
                'address'                => $validated['address'],
                'city'                   => $validated['city'],
                'state'                  => $validated['state'],
                'zip'                    => $validated['zip'],
                'main_phone'             => $validated['main_phone'],
                'contact_email'          => $validated['contact_email'] ?? null,
                'clean_time_requirement' => $validated['clean_time_requirement'],
                'credentialing_types'    => $validated['credentialing_types'] ?? [],
                'gender_restriction'     => (bool) ($validated['gender_restriction'] ?? false),
                'probation_allowed'      => (bool) ($validated['probation_allowed'] ?? true),
                'timezone'               => $validated['timezone'] ?? 'America/New_York',
                'contact1_name'          => $validated['contact1_name'] ?? null,
                'contact1_phone'         => $validated['contact1_phone'] ?? null,
                'contact1_email'         => $validated['contact1_email'] ?? null,
                'contact2_name'          => $validated['contact2_name'] ?? null,
                'contact2_phone'         => $validated['contact2_phone'] ?? null,
                'contact2_email'         => $validated['contact2_email'] ?? null,
                'status'                 => 'active',
            ]);

            // Create each meeting slot submitted in the form
            foreach ($validated['meetings'] ?? [] as $slot) {
                Meeting::create([
                    'facility_id'       => $facility->facility_id,
                    'day_of_week'       => (int) $slot['day_of_week'],
                    'week_of_month'     => (int) $slot['week_of_month'],
                    'meeting_time'      => $slot['meeting_time'],
                    'duration_minutes'  => (int) ($slot['duration_minutes'] ?? 60),
                    'format'            => $slot['format'],
                    'volunteers_needed' => (int) $slot['volunteers_needed'],
                    'status'            => 'active',
                ]);
            }

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'create_facility',
                'entity_type'    => 'facilities',
                'entity_id'      => $facility->facility_id,
                'change_details' => [
                    'created'        => true,
                    'meetings_added' => count($validated['meetings'] ?? []),
                ],
            ]);

            return redirect()->route('facilities.index')
                ->with('success', "Facility '{$facility->facility_name}' created with " . count($validated['meetings'] ?? []) . ' meeting slot(s).');
        });
    }

    /**
     * Show facility details.
     */
    public function show(Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        $meetings = $facility->meetings()
            ->withoutTrashed()
            ->where('status', 'active')
            ->orderBy('day_of_week')
            ->orderBy('week_of_month')
            ->orderBy('meeting_time')
            ->get();

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
     * Update facility details.
     */
    public function update(Request $request, Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'facility_name'          => 'sometimes|string|max:255',
            'address'                => 'sometimes|string|max:255',
            'city'                   => 'sometimes|string|max:100',
            'state'                  => 'sometimes|string|size:2',
            'zip'                    => 'sometimes|string|max:10',
            'main_phone'             => 'sometimes|string|max:20',
            'contact_email'          => 'nullable|email|max:255',
            'clean_time_requirement' => 'sometimes|integer|min:0',
            'credentialing_types'    => 'nullable|array',
            'gender_restriction'     => 'nullable|boolean',
            'probation_allowed'      => 'nullable|boolean',
            'timezone'               => 'nullable|string|max:100',
            'contact1_name'          => 'nullable|string|max:255',
            'contact1_phone'         => 'nullable|string|max:20',
            'contact1_email'         => 'nullable|email|max:255',
            'contact2_name'          => 'nullable|string|max:255',
            'contact2_phone'         => 'nullable|string|max:20',
            'contact2_email'         => 'nullable|email|max:255',
        ]);

        return DB::transaction(function () use ($validated, $facility) {
            $changes = [];
            foreach ($validated as $field => $value) {
                if ($facility->$field != $value) {
                    $changes[$field] = ['old' => $facility->$field, 'new' => $value];
                }
            }

            $facility->fill($validated)->save();

            if (!empty($changes)) {
                AuditLog::create([
                    'actor_user_id'  => auth()->id(),
                    'action'         => 'update_facility',
                    'entity_type'    => 'facilities',
                    'entity_id'      => $facility->facility_id,
                    'change_details' => $changes,
                ]);
            }

            return redirect()->route('facilities.show', $facility)
                ->with('success', 'Facility updated successfully.');
        });
    }

    /**
     * Toggle facility between active and inactive.
     */
    public function toggleStatus(Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        $oldStatus        = $facility->status;
        $facility->status = $oldStatus === 'active' ? 'inactive' : 'active';
        $facility->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'toggle_facility_status',
            'entity_type'    => 'facilities',
            'entity_id'      => $facility->facility_id,
            'change_details' => ['status' => ['old' => $oldStatus, 'new' => $facility->status]],
        ]);

        $label = $facility->status === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "Facility {$label} successfully.");
    }

    /**
     * Soft-delete facility and cascade to its meeting slots.
     */
    public function destroy(Facility $facility)
    {
        $this->authorizeCoordinatorOrAdmin();

        return DB::transaction(function () use ($facility) {
            // Soft-delete all meeting slots for this facility
            Meeting::where('facility_id', $facility->facility_id)->delete();

            $facility->delete();

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'delete_facility',
                'entity_type'    => 'facilities',
                'entity_id'      => $facility->facility_id,
                'change_details' => ['deleted' => true],
            ]);

            return redirect()->route('facilities.index')
                ->with('success', 'Facility and all meeting slots deleted successfully.');
        });
    }

}
