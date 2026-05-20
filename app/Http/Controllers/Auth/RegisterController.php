<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function handle(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'account_type' => ['required', 'in:personal,business'],
        ]);

        $role = $data['account_type'] === 'business' ? User::ROLE_BUSINESS : User::ROLE_BUYER;

        $code = sprintf("%06d", mt_rand(100000, 999999));

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'status' => User::STATUS_APPROVED,
            'verification_code' => $code,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountApprovedMail($user, $code));
        } catch (\Exception $e) {
            \Log::error("Failed to send verification email to {$user->email}: " . $e->getMessage());
        }

        return redirect()->route('verify.show', ['email' => $user->email])
            ->with('info', 'Account created successfully. A 6-digit verification code has been sent to your email.');
    }

    public function showVerifyForm(Request $request)
    {
        return view('auth.verify');
    }

    public function verifyAccount(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'verification_code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided email is not registered.',
            ])->withInput();
        }

        if ($user->status === User::STATUS_ACTIVE) {
            return redirect()->route('login')->with('success', 'Your account is already active. Please log in.');
        }

        if ($user->status !== User::STATUS_APPROVED) {
            return back()->withErrors([
                'email' => 'Your account is not approved by admin yet.',
            ])->withInput();
        }

        if ($user->verification_code !== $data['verification_code']) {
            return back()->withErrors([
                'verification_code' => 'The verification code is incorrect.',
            ])->withInput();
        }

        // Verify and activate the user
        $user->update([
            'status' => User::STATUS_ACTIVE,
            'verification_code' => null,
        ]);

        return redirect()->route('login')->with('success', 'Your account has been successfully verified and activated! You can now log in.');
    }
}
