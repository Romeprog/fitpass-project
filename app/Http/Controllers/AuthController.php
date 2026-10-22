<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        return view('login', [
            'rememberedEmail' => $request->cookie('remembered_email'),
        ]);
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // A suspended member (set in the admin panel) can not log in.
        // Only said when the password is right, so the message does not reveal who has an account.
        $member = User::where('email', $credentials['email'])->where('role', 'user')->first();
        if ($member && $member->status === 'suspended' && Hash::check($credentials['password'], $member->password)) {
            return back()
                ->withErrors(['email' => 'This account is suspended. Please contact the admin.'])
                ->onlyInput('email');
        }

        // Members only: admin accounts must use the separate admin login (/admin/login)
        if (Auth::attempt($credentials + ['role' => 'user'])) {
            $request->session()->regenerate();

            // Remember (or forget) the email for the next visit
            if ($request->boolean('remember')) {
                Cookie::queue('remembered_email', $credentials['email'], 60 * 24 * 30); // 30 days
            } else {
                Cookie::queue(Cookie::forget('remembered_email'));
            }

            return redirect()->route('home');
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}