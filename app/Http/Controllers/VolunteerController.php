<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Models\Volunteer;
use App\Models\VolunteerCredential;
use App\Models\CredentialType;
use App\Models\User;
use App\Models\Facility;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class VolunteerController extends Controller
{
    /**
     * List volunteers.
     */
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = Volunteer::withoutTrashed();

        // Search by name or email
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', $search)
                  ->orWhere('first_name', 'like', $search)
                  ->orWhere('last_name', 'like', $search);
            });
        }

        // Filter by probation/status
        if ($request->filled('status')) {
            if ($request->status === 'probation') {
                $query->where('probation_status', 'active_probation');
            } elseif ($request->status === 'active') {
                $query->where('probation_status', 'not_probation');
            }
        }

        // Filter by clean time (years)
        if ($request->filled('clean_time')) {
            [$min, $max] = match($request->clean_time) {
                '0-1'  => [0, 1],
                '1-2'  => [1, 2],
                '2-5'  => [2, 5],
                '5+'   => [5, 999],
                default => [0, 999],
            };
            $query->whereBetween('clean_date', [
                now()->subYears($max)->toDateString(),
                now()->subYears($min)->toDateString(),
            ]);
        }

        $volunteers = $query->with(['credentials.credentialType'])->paginate(15);

        return view('coordinator.volunteers', compact('volunteers'));
    }

    /**
     * Show volunteer registration form.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Store a new volunteer.
     */
    public function store(StoreVolunteerRequest $request)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            // Create user account
            $user = User::create([
                'email'         => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'roles'         => ['volunteer'],
            ]);

            // Create volunteer record
            $volunteer = Volunteer::create([
                'email'              => $validated['email'],
                'first_name'         => $validated['first_name'],
                'last_name'          => $validated['last_name'],
                'dob'                => $validated['dob'],
                'phone'              => $validated['phone'],
                'gender'             => $validated['gender'],
                'probation_status'   => ($validated['on_probation'] ?? false) ? 'active_probation' : 'not_probation',
                'clean_date'         => $validated['clean_date'] ?? null,
                'treatment_facility' => ($validated['has_treatment_facility'] ?? false) ? '1' : null,
                'facility_name'      => $validated['treatment_facility_name'] ?? null,
                'discharge_date'     => $validated['discharge_date'] ?? null,
                'neighborhood'       => $validated['neighborhood'] ?? null,
                'bus_line'           => $validated['bus_line'] ?? null,
            ]);

            return auth()->check()
                ? redirect()->route('volunteers.index')->with('success', "Volunteer '{$volunteer->first_name} {$volunteer->last_name}' added successfully.")
                : redirect()->route('login')->with('success', 'Account created! You can now log in.');
        });
    }

    /**
     * Show volunteer profile.
     */
    public function show(Volunteer $volunteer)
    {
        $this->authorizeView($volunteer);

        $volunteer->load('credentials.credentialType');

        // Coordinators/admins see a read-only version of the profile
        $user  = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];
        $isCoordinator = in_array('coordinator', $roles) || in_array('admin', $roles);
        $readOnly = $isCoordinator && $user->email !== $volunteer->email;

        return view('volunteer.profile', compact('volunteer', 'readOnly'));
    }

    /**
     * Show coordinator edit form.
     */
    public function edit(Volunteer $volunteer)
    {
        $this->authorizeCoordinatorOrAdmin();

        $volunteer->load(['credentials.credentialType', 'credentials.facility']);
        $facilities = Facility::orderBy('facility_name')->get();
        $credentialTypes = CredentialType::orderBy('name')->get();

        return view('coordinator.volunteer-edit', compact('volunteer', 'facilities', 'credentialTypes'));
    }

    /**
     * Coordinator update of volunteer info.
     */
    public function coordinatorUpdate(Request $request, Volunteer $volunteer)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email|unique:volunteers,email,' . $volunteer->volunteer_id . ',volunteer_id',
            'phone'             => 'required|string|max:30',
            'dob'               => 'required|date|before_or_equal:today',
            'gender'            => 'required|string|max:50',
            'clean_date'        => 'required|date|before_or_equal:today',
            'probation_status'  => 'required|in:not_probation,active_probation',
            'treatment_facility'=> 'nullable|string|max:255',
            'facility_name'     => 'nullable|string|max:255',
            'discharge_date'    => 'nullable|date',
            'neighborhood'      => 'nullable|string|max:255',
            'bus_line'          => 'nullable|string|max:100',
            'is_sms_deliverable'=> 'boolean',
        ]);

        $volunteer->fill($validated)->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'coordinator_update_volunteer',
            'entity_type'    => 'volunteers',
            'entity_id'      => $volunteer->volunteer_id,
            'change_details' => $validated,
        ]);

        return redirect()->route('volunteers.edit', $volunteer)
            ->with('success', 'Volunteer updated successfully.');
    }

    /**
     * Add a credential to a volunteer.
     */
    public function storeCredential(Request $request, Volunteer $volunteer)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'facility_id'        => 'required|exists:facilities,facility_id',
            'credential_type_id' => 'required|exists:credential_types,credential_type_id',
            'status'             => 'required|in:pending,approved,denied',
            'approval_date'      => 'nullable|date',
            'expiration_date'    => 'nullable|date',
            'notes'              => 'nullable|string|max:500',
        ]);

        $validated['volunteer_id'] = $volunteer->volunteer_id;

        VolunteerCredential::create($validated);

        return redirect()->route('volunteers.edit', $volunteer)
            ->with('success', 'Credential added successfully.');
    }

    /**
     * Update an existing credential.
     */
    public function updateCredential(Request $request, Volunteer $volunteer, VolunteerCredential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'status'          => 'required|in:pending,approved,denied',
            'approval_date'   => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'notes'           => 'nullable|string|max:500',
        ]);

        $credential->fill($validated)->save();

        return redirect()->route('volunteers.edit', $volunteer)
            ->with('success', 'Credential updated successfully.');
    }

    /**
     * Delete a credential.
     */
    public function destroyCredential(Volunteer $volunteer, VolunteerCredential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        $credential->delete();

        return redirect()->route('volunteers.edit', $volunteer)
            ->with('success', 'Credential removed.');
    }

    /**
     * Update volunteer.
     */
    public function update(StoreVolunteerRequest $request, Volunteer $volunteer)
    {
        $this->authorizeEdit($volunteer);

        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $volunteer) {
            $changes = [];

            // Update user account
            $userChanges = [];
            if ($validated['email'] !== $volunteer->user->email) {
                $userChanges['email'] = [
                    'old' => $volunteer->user->email,
                    'new' => $validated['email'],
                ];
                $volunteer->user->email = $validated['email'];
            }

            if ($validated['first_name'] !== $volunteer->user->first_name) {
                $userChanges['first_name'] = [
                    'old' => $volunteer->user->first_name,
                    'new' => $validated['first_name'],
                ];
                $volunteer->user->first_name = $validated['first_name'];
            }

            if ($validated['last_name'] !== $volunteer->user->last_name) {
                $userChanges['last_name'] = [
                    'old' => $volunteer->user->last_name,
                    'new' => $validated['last_name'],
                ];
                $volunteer->user->last_name = $validated['last_name'];
            }

            if (!empty($validated['password'])) {
                $userChanges['password'] = ['old' => '***', 'new' => '***'];
                $volunteer->user->password = Hash::make($validated['password']);
            }

            if (!empty($userChanges)) {
                $volunteer->user->save();
                $changes['user'] = $userChanges;
            }

            // Update volunteer record
            $volunteerFields = [
                'phone',
                'date_of_birth',
                'gender',
                'address_street',
                'address_city',
                'address_state',
                'address_zip',
                'treatment_facility_id',
                'is_self_recovery',
                'clean_date',
                'bio',
                'emergency_contact_name',
                'emergency_contact_phone',
            ];

            foreach ($volunteerFields as $field) {
                if (isset($validated[$field]) && $volunteer->$field !== $validated[$field]) {
                    $changes[$field] = [
                        'old' => $volunteer->$field,
                        'new' => $validated[$field],
                    ];
                    $volunteer->$field = $validated[$field];
                }
            }

            // Handle array fields
            if (isset($validated['certifications'])) {
                $newCerts = !empty($validated['certifications'])
                    ? json_encode($validated['certifications'])
                    : null;
                if ($volunteer->certifications !== $newCerts) {
                    $changes['certifications'] = [
                        'old' => $volunteer->certifications,
                        'new' => $newCerts,
                    ];
                    $volunteer->certifications = $newCerts;
                }
            }

            if (isset($validated['languages'])) {
                $newLangs = !empty($validated['languages'])
                    ? json_encode($validated['languages'])
                    : null;
                if ($volunteer->languages !== $newLangs) {
                    $changes['languages'] = [
                        'old' => $volunteer->languages,
                        'new' => $newLangs,
                    ];
                    $volunteer->languages = $newLangs;
                }
            }

            $volunteer->save();

            // Audit log
            if (!empty($changes)) {
                AuditLog::create([
                    'actor_user_id'  => auth()->id(),
                    'action'         => 'update_volunteer',
                    'entity_type'    => 'volunteers',
                    'entity_id'      => $volunteer->volunteer_id,
                    'change_details' => $changes,
                ]);
            }

            return redirect()->route('volunteers.show', $volunteer)
                ->with('success', 'Volunteer updated successfully.');
        });
    }

    /**
     * Soft delete volunteer.
     */
    public function destroy(Volunteer $volunteer)
    {
        $this->authorizeCoordinatorOrAdmin();

        // Check for active meetings
        $activeMeetings = $volunteer->assignments()
            ->whereIn('status', ['scheduled', 'pending_confirmation'])
            ->count();

        if ($activeMeetings > 0) {
            return back()->with('error', 'Cannot delete volunteer with active meeting assignments.');
        }

        return DB::transaction(function () use ($volunteer) {
            $volunteer->delete();

            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'delete_volunteer',
                'entity_type'    => 'volunteers',
                'entity_id'      => $volunteer->volunteer_id,
                'change_details' => ['deleted' => true],
            ]);

            return redirect()->route('volunteers.index')
                ->with('success', 'Volunteer deleted successfully.');
        });
    }

    /**
     * Authorize view access.
     */
    private function authorizeView(Volunteer $volunteer)
    {
        // Match by email — volunteers have no user_id FK
        if (auth()->user()->email === $volunteer->email) {
            return;
        }

        $this->authorizeCoordinatorOrAdmin();
    }

    /**
     * Authorize edit access.
     */
    private function authorizeEdit(Volunteer $volunteer)
    {
        // Match by email — volunteers have no user_id FK
        if (auth()->user()->email === $volunteer->email) {
            return;
        }

        $this->authorizeCoordinatorOrAdmin();
    }
}
