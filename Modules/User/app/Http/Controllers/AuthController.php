<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('user::auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $loginInput = $credentials['username'];

        // Determine if input is formatted as email or username
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $primaryField = $isEmail ? 'email' : 'username';

        if (Auth::attempt([$primaryField => $loginInput, 'password' => $credentials['password']], $remember)
            || ($primaryField !== 'email' && Auth::attempt(['email' => $loginInput, 'password' => $credentials['password']], $remember))
            || ($primaryField === 'email' && Auth::attempt(['username' => $loginInput, 'password' => $credentials['password']], $remember))) {
            
            $user = Auth::user();
            if (isset($user->is_active) && ! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('username', 'remember'))
                    ->withErrors([
                        'username' => 'Your account is currently inactive. Please contact the administrator.',
                    ]);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors([
                'username' => 'Invalid username or password.',
            ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been successfully signed out.');
    }
}

