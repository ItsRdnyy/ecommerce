<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function handle(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = \App\Models\User::where('email', $data['email'])->first();
        $validPassword = false;
        $isPlainPassword = false;

        if ($user) {
            $isPlainPassword = $user->password === $data['password'];

            if ($isPlainPassword) {
                $validPassword = true;
            } else {
                try {
                    $validPassword = Hash::check($data['password'], $user->password);
                } catch (\RuntimeException $exception) {
                    $validPassword = false;
                }
            }
        }

        if ($user && $validPassword) {
            // Check user status
            if (!$user->isAdmin()) {
                if ($user->status === \App\Models\User::STATUS_PENDING) {
                    return back()->withErrors([
                        'email' => 'Your account is pending admin approval.',
                    ])->onlyInput('email', 'redirect');
                }

                if ($user->status === \App\Models\User::STATUS_APPROVED) {
                    return redirect()->route('verify.show', ['email' => $user->email])
                        ->with('info', 'Your account has been approved by admin. Please enter the verification code to activate it.');
                }

                if ($user->status !== \App\Models\User::STATUS_ACTIVE) {
                    return back()->withErrors([
                        'email' => 'Your account is ' . $user->status . '.',
                    ])->onlyInput('email', 'redirect');
                }
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isBusiness()) {
                return redirect()->route('business.dashboard');
            }

            if ($user->isBuyer()) {
                return redirect()->route('home');
            }

            $redirect = $request->input('redirect');

            if ($redirect && $this->isSafeRedirect($redirect)) {
                return redirect()->to($redirect);
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email', 'redirect');
    }

    private function isSafeRedirect(string $url): bool
    {
        if (str_starts_with($url, '/')) {
            return true;
        }

        $parsed = parse_url($url);
        if (empty($parsed['host']) || empty($parsed['scheme'])) {
            return false;
        }

        return ($parsed['scheme'] . '://' . $parsed['host']) === parse_url(url('/'), PHP_URL_SCHEME) . '://' . parse_url(url('/'), PHP_URL_HOST);
    }

    private function isAuthPage(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);

        return in_array($path, [
            parse_url(route('login'), PHP_URL_PATH),
            parse_url(route('register'), PHP_URL_PATH),
            parse_url(route('password.reset', ['token' => request('token') ?? 'placeholder']), PHP_URL_PATH),
        ], true);
    }
}
