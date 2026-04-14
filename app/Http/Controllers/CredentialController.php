<?php

namespace App\Http\Controllers;

use App\Models\VolunteerCredential;
use App\Models\CredentialType;
use App\Models\Volunteer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CredentialController extends Controller
{
    /**
     * List credentials with filters and expiration alerts.
     */
    public function index(Request $request)
    {
        $this->authorizeCoordinatorOrAdmin();

        $query = VolunteerCredential::with(['volunteer', 'credentialType', 'facility']);

        if ($request->filled('volunteer_id')) {
            $query->where('volunteer_id', $request->volunteer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('credential_type_id')) {
            $query->where('credential_type_id', $request->credential_type_id);
        }

        if ($request->boolean('expiring_soon')) {
            $query->where('status', 'approved')
                  ->whereDate('expiration_date', '<=', now()->addDays(30))
                  ->whereDate('expiration_date', '>', now());
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('volunteer', function ($q) use ($search) {
                $q->where('first_name', 'like', $search)
                  ->orWhere('last_name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        $credentials    = $query->orderBy('expiration_date')->paginate(20);
        $credentialTypes = CredentialType::orderBy('name')->get();
        $expiringCount  = $this->getExpiringCredentialsCount();

        // Credentials expiring within 30 days for the alert banner
        $expiringSoon = VolunteerCredential::with(['volunteer', 'credentialType'])
            ->where('status', 'approved')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>', now())
            ->orderBy('expiration_date')
            ->get();

        return view('coordinator.credentials', compact(
            'credentials', 'credentialTypes', 'expiringCount', 'expiringSoon'
        ));
    }

    /**
     * Approve a credential.
     */
    public function approve(VolunteerCredential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        $credential->status        = 'approved';
        $credential->approval_date = now()->toDateString();
        $credential->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'credential_approved',
            'entity_type'    => 'volunteer_credentials',
            'entity_id'      => $credential->credential_id,
            'change_details' => ['status' => 'approved'],
        ]);

        return back()->with('success', 'Credential approved.');
    }

    /**
     * Deny a credential.
     */
    public function deny(Request $request, VolunteerCredential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'denial_reason' => 'required|string|max:500',
        ]);

        $credential->status = 'denied';
        $credential->notes  = $validated['denial_reason'];
        $credential->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'credential_denied',
            'entity_type'    => 'volunteer_credentials',
            'entity_id'      => $credential->credential_id,
            'change_details' => ['status' => 'denied', 'reason' => $validated['denial_reason']],
        ]);

        return back()->with('success', 'Credential denied.');
    }

    /**
     * Renew a credential (extend expiration, reset to approved).
     */
    public function renew(Request $request, VolunteerCredential $credential)
    {
        $this->authorizeCoordinatorOrAdmin();

        $validated = $request->validate([
            'expiration_date' => 'required|date|after:today',
        ]);

        $credential->expiration_date = $validated['expiration_date'];
        $credential->approval_date   = now()->toDateString();
        $credential->status          = 'approved';
        $credential->save();

        AuditLog::create([
            'actor_user_id'  => auth()->id(),
            'action'         => 'credential_renewed',
            'entity_type'    => 'volunteer_credentials',
            'entity_id'      => $credential->credential_id,
            'change_details' => ['new_expiration_date' => $validated['expiration_date']],
        ]);

        return back()->with('success', 'Credential renewed.');
    }

    /**
     * Count credentials expiring within 30 days.
     */
    private function getExpiringCredentialsCount(): int
    {
        return VolunteerCredential::where('status', 'approved')
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>', now())
            ->count();
    }
}
