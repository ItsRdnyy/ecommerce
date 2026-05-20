<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use App\Events\UserRegistered;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function handle(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'account_type' => ['required', 'in:personal,business'],
        ];

        if ($request->account_type === 'business') {
            $rules['business_name'] = ['required', 'string', 'max:255'];
            $rules['tax_id'] = ['required', 'string', 'max:50'];
        }

        $data = $request->validate($rules);

        $role = $data['account_type'] === 'business' ? User::ROLE_BUSINESS : User::ROLE_BUYER;
        $status = $data['account_type'] === 'business' ? User::STATUS_PENDING : User::STATUS_APPROVED;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $role,
            'status' => $status,
        ]);

        if ($user->isBusiness()) {
            \App\Models\BusinessProfile::create([
                'user_id' => $user->id,
                'business_name' => $data['business_name'],
                'tax_id' => $data['tax_id'],
            ]);
        }

        event(new UserRegistered($user));

        if ($user->status === User::STATUS_PENDING) {
            return redirect()->route('login')->with('info', 'Registration successful. Your business account is pending admin approval.');
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

        if (!$user->verification_code || $user->verification_code !== $data['verification_code']) {
            return back()->withErrors([
                'verification_code' => 'The verification code is incorrect.',
            ])->withInput();
        }

        if ($user->verification_expires_at && $user->verification_expires_at->isPast()) {
            return back()->withErrors([
                'verification_code' => 'The verification code has expired. Please request a new one.',
            ])->withInput();
        }

        // Verify and activate the user
        $user->update([
            'status' => User::STATUS_ACTIVE,
            'verification_code' => null,
            'verification_expires_at' => null,
        ]);

        return redirect()->route('login')->with('success', 'Your account has been successfully verified and activated! You can now log in.');
    }

    public function resendVerification(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || $user->status !== User::STATUS_APPROVED) {
            return back()->with('error', 'Unable to resend verification code.');
        }

        $code = sprintf("%06d", random_int(100000, 999999));
        $user->update([
            'verification_code' => $code,
            'verification_expires_at' => now()->addMinutes(30),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountApprovedMail($user, $code));
            return back()->with('success', 'A new verification code has been sent to your email.');
        } catch (\Exception $e) {
            \Log::error("Failed to resend verification email to {$user->email}: " . $e->getMessage());
            return back()->with('error', 'Failed to send email. Please try again later.');
        }
    }
}
