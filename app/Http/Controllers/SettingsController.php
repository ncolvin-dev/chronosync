<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * SettingsController
 *
 * Manages account-level settings that apply to the authenticated User record
 * (email address and password). These are distinct from volunteer profile
 * fields (name, clean date, etc.) which live on the Volunteer model.
 *
 * Email changes require a two-step sync: the users table is updated first,
 * then the volunteers table is updated to match — because volunteers are
 * looked up by email across the app, any mismatch would break profile access.
 */
class SettingsController extends Controller
{
    /**
     * Render the account settings page.
     */
    public function index()
    {
        return view('settings');
    }

    /**
     * Update the authenticated user's email address.
     *
     * Validation ensures the new email is not already taken by another user.
     * After saving to the users table, the corresponding volunteer record (if
     * any) is updated to the new email so the email-based join stays in sync.
     *
     * Uses the user_id primary key name in the unique rule to correctly exclude
     * the current user from the uniqueness check.
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'new_email'     => 'required|email|max:255|unique:users,email,' . auth()->id() . ',user_id',
            'confirm_email' => 'required|same:new_email',
        ], [
            'new_email.unique'   => 'That email address is already in use.',
            'confirm_email.same' => 'Email addresses do not match.',
        ]);

        $user     = auth()->user();
        $oldEmail = $user->email;
        $user->email = $validated['new_email'];
        $user->save();

        // Volunteers are matched to user accounts by email. Update the volunteer
        // record to keep the join intact — a mismatch would cause the volunteer
        // to lose access to their profile and assignments.
        Volunteer::where('email', $oldEmail)->update(['email' => $validated['new_email']]);

        return back()->with('email_success', 'Email address updated successfully.');
    }

    /**
     * Update the authenticated user's password.
     *
     * The current password is verified before accepting the change, preventing
     * an attacker with an unattended session from locking the user out.
     * Passwords are stored as bcrypt hashes in password_hash (not the default
     * Laravel `password` column name).
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:10|confirmed',
        ], [
            'new_password.min'       => 'New password must be at least 10 characters.',
            'new_password.confirmed' => 'New passwords do not match.',
        ]);

        $user = auth()->user();

        // Reject if the supplied current password doesn't match the stored hash.
        if (!Hash::check($validated['current_password'], $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('pw_error', true);
        }

        $user->password_hash = Hash::make($validated['new_password']);
        $user->save();

        return back()->with('password_success', 'Password updated successfully.');
    }
}
