<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Models\Volunteer;
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

        $query = Volunteer::with('user', 'facility')->withoutTrashed();

        // Filter by facility
        if ($request->filled('facility_id')) {
            $query->where('treatment_facility_id', $request->facility_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('email', 'like', $search)
                  ->orWhere('first_name', 'like', $search)
                  ->orWhere('last_name', 'like', $search);
            });
        }

        $volunteers = $query->paginate(15);

        return view('coordinator.volunteers', compact('volunteers'));
    }

    /**
     * Show volunteer registration form.
     */
    public function create()
    {
        $facilities = Facility::where('status', 'active')
            ->orderBy('facility_name')
            ->get();

        return view('placeholder.coming-soon', compact('facilities'));
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
                'email' => $validated['email'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'password' => Hash::make($validated['password']),
                'roles' => json_encode(['volunteer']),
                'is_active' => true,
            ]);

            // Create volunteer record
            $volunteerData = [
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address_street' => $validated['address_street'],
                'address_city' => $validated['address_city'],
                'address_state' => $validated['address_state'],
                'address_zip' => $validated['address_zip'],
                'treatment_facility_id' => $validated['treatment_facility_id'] ?? null,
                'is_self_recovery' => $validated['is_self_recovery'] ?? false,
                'clean_date' => $validated['clean_date'] ?? null,
                'certifications' => !empty($validated['certifications'])
                    ? json_encode($validated['certifications'])
                    : null,
                'languages' => !empty($validated['languages'])
                    ? json_encode($validated['languages'])
                    : null,
                'bio' => $validated['bio'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
                'is_active' => true,
            ];

            $volunteer = Volunteer::create($volunteerData);

            // Initialize availability (35 slots: 5 weeks × 7 days, all false by default)
            $volunteer->availability = json_encode(array_fill(0, 35, false));
            $volunteer->save();

            // Audit log
            AuditLog::create([
                'actor_user_id'  => auth()->id(),
                'action'         => 'create_volunteer',
                'entity_type'    => 'volunteers',
                'entity_id'      => $volunteer->volunteer_id,
                'change_details' => ['created' => true],
            ]);

            return redirect()->route('volunteers.show', $volunteer)
                ->with('success', 'Volunteer registered successfully.');
        });
    }

    /**
     * Show volunteer profile.
     */
    public function show(Volunteer $volunteer)
    {
        $this->authorizeView($volunteer);

        return view('volunteer.profile', compact('volunteer'));
    }

    /**
     * Show edit form.
     */
    public function edit(Volunteer $volunteer)
    {
        $this->authorizeEdit($volunteer);

        $facilities = Facility::where('status', 'active')
            ->orderBy('facility_name')
            ->get();

        return view('placeholder.coming-soon', compact('volunteer', 'facilities'));
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
     * Authorize that user is coordinator or admin.
     */
    private function authorizeCoordinatorOrAdmin()
    {
        $user = auth()->user();
        $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true) ?? [];

        if (!in_array('coordinator', $roles) && !in_array('admin', $roles)) {
            abort(403);
        }
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
