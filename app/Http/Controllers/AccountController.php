<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * "My Account" page for members: change your own password.
 */
class AccountController extends Controller
{
    public function show(Request $request): View
    {
        return view('account', ['user' => $request->user()]);
    }

    /**
     * Change the logged-in member's password.
     * The current password must be right, and the new one must be different.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Your current password is not correct.',
            'password.different' => 'Choose a password that is different from your current one.',
        ]);

        // The User model scrambles the password for us.
        // A new remember token signs out "remember me" logins on other devices.
        $request->user()->forceFill([
            'password' => $data['password'],
            'remember_token' => Str::random(60),
        ])->save();

        // This browser stays logged in
        $request->session()->regenerate();

        return back()->with('status', 'Your password has been changed.');
    }
}
