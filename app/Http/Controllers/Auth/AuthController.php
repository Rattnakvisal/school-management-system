<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showLogin(Request $request)
    {
        // Ensure fresh CSRF token whenever auth page is loaded.
        $request->session()->regenerateToken();
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $this->authService->login(
            ['email' => $validated['email'], 'password' => $validated['password']],
            $request->boolean('remember')
        );

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Invalid credentials'])
                ->withInput($request->only('email'));
        }

        if (isset($user->is_active) && !$user->is_active) {
            $this->authService->logout();

            return back()
                ->withErrors(['email' => 'Your account is inactive. Please contact administrator.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return $this->authService->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        $role = (string) ($request->user()?->role ?? '');

        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $role === 'teacher'
            ? redirect()->to('/')
            : redirect()->route('home');
    }
}
