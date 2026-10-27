<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

/**
 * "Forgot password" for members.
 *
 * Uses Laravel's own password broker: it makes a one-time token, saves a scrambled
 * copy in the password_reset_tokens table, and emails the member a link that works
 * for 60 minutes. Only member accounts (role = user) can be reset here; the admin
 * has a separate login. Suspended members can not reset their password, the same
 * way they can not log in.
 */
class PasswordResetController extends Controller
{
    /**
     * Page where the member types their email.
     */
    public function showForgot(): View
    {
        return view('forgot-password');
    }

    /**
     * Email the reset link.
     *
     * The answer is the same whether or not the email belongs to a member (or
     * the member is suspended), so nobody can use this page to find out who
     * has an account.
     */
    public function sendLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $message = 'If that email belongs to a FitPass member, a reset link is on its way. Check your inbox and your spam folder.';

        // Suspended member: send nothing, but show the same message
        if ($this->isSuspended($data['email'])) {
            return back()->with('status', $message);
        }

        try {
            Password::sendResetLink([
                'email' => $data['email'],
                'role' => 'user',
            ]);
        } catch (Throwable $e) {
            // For example: the mail server is not set up, or the host blocks email
            report($e);

            return back()
                ->withErrors(['email' => 'We could not send the email right now. Please try again later.'])
                ->onlyInput('email');
        }

        return back()->with('status', $message);
    }

    /**
     * Page opened from the link in the email, where the member picks a new password.
     */
    public function showReset(Request $request, string $token): View
    {
        return view('reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Save the new password if the link is still good.
     */
    public function reset(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $invalidLink = 'This reset link is invalid or has expired. Please ask for a new one.';

        // Suspended after the link was sent? The link stops working.
        if ($this->isSuspended($data['email'])) {
            return back()
                ->withErrors(['email' => $invalidLink])
                ->withInput($request->only('email'));
        }

        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $data['token'],
                'role' => 'user',
            ],
            function (User $user, string $password) {
                // The User model scrambles the password for us
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Your password has been changed. You can sign in now.');
        }

        return back()
            ->withErrors(['email' => $invalidLink])
            ->withInput($request->only('email'));
    }

    /**
     * Is this email a member that the admin has suspended?
     */
    private function isSuspended(string $email): bool
    {
        $member = User::where('email', $email)->where('role', 'user')->first();

        return $member !== null && $member->status === 'suspended';
    }
}