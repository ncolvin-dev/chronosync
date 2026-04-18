<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings');
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'new_email'     => 'required|email|max:255|unique:users,email,' . auth()->id() . ',user_id',
            'confirm_email' => 'required|same:new_email',
        ], [
            'new_email.unique'      => 'That email address is already in use.',
            'confirm_email.same'    => 'Email addresses do not match.',
        ]);

        $user     = auth()->user();
        $oldEmail = $user->email;
        $user->email = $validated['new_email'];
        $user->save();

        // Keep volunteer record in sync — volunteers are matched by email.
        Volunteer::where('email', $oldEmail)->update(['email' => $validated['new_email']]);

        return back()->with('email_success', 'Email address updated successfully.');
    }

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

        if (!Hash::check($validated['current_password'], $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('pw_error', true);
        }

        $user->password_hash = Hash::make($validated['new_password']);
        $user->save();

        return back()->with('password_success', 'Password updated successfully.');
    }
}
