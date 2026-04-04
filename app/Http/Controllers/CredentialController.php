<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Volunteer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CredentialController extends Controller
{
    /**
     * List credentials with expiration alerts.
     */
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = Credential::with('volunteer.user');

        // Filter by volunteer
        if ($request->filled('volunteer_id')) {
            $query->where('volunteer_id', $request->volunteer_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('credential_type')) {
            $query->where('credential_type', $request->credential_type);
        }

        // Show expiring soon
        if ($request->boolean('expiring_soon')) {
            $query->whereDate('expiration_date', '<=', now()->addDays(30))
                  ->whereDate('expiration_date', '>', now());
        }

        $credentials = $query->orderBy('expiration_date')->paginate(15);
        $expiringCount = $this->getExpiringCredentialsCount();

        return view('coordinator.credentials', compact('credentials', 'expiringCount'));
    }

    /**
     * Create credential record.
     */
    public function store(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'volunteer_id' => 'required|exists:volunteers,id',
            'credential_type' => 'required|in:cpr,first_aid,background_check,license,certification,other',
            'credential_number' => 'nullable|string|max:100',
            'issued_date' => 'required|date|before_or_equal:today',
            'expiration_date' => 'required|date|after:issued_date',
            'issuing_authority' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated) {
            $credential = Credential::create([
                'volunteer_id' => $validated['volunteer_id'],
                'credential_type' => $validated['credential_type'],
                'credential_number' => $validated['credential_number'] ?? null,
                'issued_date' => $validated['issued_date'],
                'expiration_date' => $validated['expiration_date'],
                'issuing_authority' => $validated['issuing_authority'] ?? null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'credential_created',
                'model_type' => Credential::class,
                'model_id' => $credential->id,
                'changes' => ['created' => $credential->toArray()],
            ]);

            return redirect()->route('credentials.index')
                ->with('success', 'Credential record created successfully.');
        });
    }

    /**
     * Approve credential.
     */
    public function approve(Credential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        return DB::transaction(function () use ($credential) {
            $oldStatus = $credential->status;

            $credential->status = 'active';
            $credential->approved_at = now();
            $credential->approved_by = auth()->id();
            $credential->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'credential_approved',
                'model_type' => Credential::class,
                'model_id' => $credential->id,
                'changes' => [
                    'status' => [
                        'old' => $oldStatus,
                        'new' => 'active',
                    ],
                    'approved_at' => now(),
                ],
            ]);

            return back()->with('success', 'Credential approved successfully.');
        });
    }

    /**
     * Deny credential.
     */
    public function deny(Request $request, Credential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'denial_reason' => 'required|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $credential) {
            $oldStatus = $credential->status;

            $credential->status = 'rejected';
            $credential->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'credential_denied',
                'model_type' => Credential::class,
                'model_id' => $credential->id,
                'changes' => [
                    'status' => [
                        'old' => $oldStatus,
                        'new' => 'rejected',
                    ],
                    'denial_reason' => $validated['denial_reason'],
                ],
            ]);

            return back()->with('success', 'Credential denied and volunteer notified.');
        });
    }

    /**
     * Renew credential.
     */
    public function renew(Request $request, Credential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'issued_date' => 'required|date',
            'expiration_date' => 'required|date|after:issued_date',
            'credential_number' => 'nullable|string|max:100',
        ]);

        return DB::transaction(function () use ($validated, $credential) {
            // Preserve history by keeping old credential
            $oldCredentialData = $credential->toArray();

            // Create new credential with updated dates
            $newCredential = Credential::create([
                'volunteer_id' => $credential->volunteer_id,
                'credential_type' => $credential->credential_type,
                'credential_number' => $validated['credential_number'] ?? $credential->credential_number,
                'issued_date' => $validated['issued_date'],
                'expiration_date' => $validated['expiration_date'],
                'issuing_authority' => $credential->issuing_authority,
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'notes' => 'Renewal of credential #' . $credential->id,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'credential_renewed',
                'model_type' => Credential::class,
                'model_id' => $newCredential->id,
                'changes' => [
                    'previous_credential_id' => $credential->id,
                    'new_expiration_date' => $validated['expiration_date'],
                    'previous_expiration_date' => $credential->expiration_date,
                ],
            ]);

            return redirect()->route('credentials.index')
                ->with('success', 'Credential renewed successfully.');
        });
    }

    /**
     * Get credentials expiring within 30 days.
     */
    public function getExpiringCredentials()
    {
        $this->authorizeCoordinatorOrAdmin();

        $credentials = Credential::where('status', 'active')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>', now())
            ->with('volunteer.user')
            ->orderBy('expiration_date')
            ->get();

        return view('placeholder.coming-soon', compact('credentials'));
    }

    /**
     * Get count of expiring credentials.
     */
    private function getExpiringCredentialsCount(): int
    {
        return Credential::where('status', 'active')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>', now())
            ->count();
    }

    /**
     * Authorize coordinator or admin.
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
