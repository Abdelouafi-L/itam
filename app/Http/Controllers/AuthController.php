<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Forms
    |--------------------------------------------------------------------------
    */

    /**
     * Show the login form.
     * Raw PHP equivalent: login.php loading the form HTML
     */
    public function showLogin()
    {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Show the registration form.
     * Raw PHP equivalent: register.php loading the form HTML
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Show the reset password form.
     */
    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Handle login form submission.
     * Raw PHP equivalent: login.php processing $_POST
     */
    public function login(Request $request)
    {
        // Step 1 — Validate input
        // Replaces your manual Validator class entirely
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        // Step 2 — Rate limiting
        // Replaces your manual RateLimiter class
        $key = 'login.' . Str::lower($request->email) . '.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withErrors([
                    'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes."
                ])
                ->withInput($request->only('email'));
        }

        // Step 3 — Attempt login
        // Replaces your manual password_verify() + session handling
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Clear rate limiter on success
            RateLimiter::clear($key);

            // Regenerate session to prevent fixation attacks
            // Replaces your manual session_regenerate()
            $request->session()->regenerate();

            return redirect()
                ->intended(route('dashboard'))
                ->with('success', 'Bienvenue ' . Auth::user()->first_name . ' !');
        }

        // Step 4 — Login failed
        RateLimiter::hit($key);

        return back()
            ->withErrors([
                'email' => 'Ces identifiants ne correspondent à aucun compte.'
            ])
            ->withInput($request->only('email'));
    }

    /**
     * Handle logout.
     * Raw PHP equivalent: logout.php
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate and regenerate session
        // Replaces your manual session_destroy() + setcookie()
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Handle registration form submission.
     * Creates Employee record first, then User record linked to it.
     * Raw PHP equivalent: register.php processing $_POST with two INSERTs.
     */
    public function register(Request $request)
    {
        // Step 1 — Validate all fields
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'email'      => ['required', 'email', 'unique:employees,email'],
            'password'   => ['required', 'min:8', 'confirmed'],
        ]);

    // Step 2 — Get or create default Administration department
    $department = \App\Models\Department::firstOrCreate(
        ['name' => 'Administration'],
        ['description' => 'Département administratif système']
    );

    // Step 3 — Create the Employee record
    $employee = \App\Models\Employee::create([
        'department_id' => $department->id,
        'first_name'    => $validated['first_name'],
        'last_name'     => $validated['last_name'],
        'email'         => $validated['email'],
        'is_active'     => true,
    ]);

        // Step 3 — Get or create the default role
        // First registered user gets Administrateur role
        $role = \App\Models\Role::firstOrCreate(
            ['name' => 'Administrateur'],
            ['description' => 'Accès complet au système']
        );

        // Step 4 — Create the User record linked to the Employee
        $user = \App\Models\User::create([
            'employee_id' => $employee->id,
            'role_id'     => $role->id,
            'password'    => \Illuminate\Support\Facades\Hash::make(
                                $validated['password']
                            ),
            'is_active'   => true,
        ]);

        // Step 5 — Log the user in immediately
        Auth::login($user);

        // Step 6 — Redirect to dashboard
        return redirect()
            ->route('dashboard')
            ->with('success',
                'Compte créé avec succès. Bienvenue ' .
                $user->first_name . ' !'
            );
    }

    /**
     * Handle forgot password form submission.
     * Sends reset email if account exists.
     * Raw PHP equivalent: your forgot-password.php processing $_POST
     */
    public function sendResetLink(Request $request)
    {
        // Step 1 — Validate email format
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Step 2 — Find the employee by email
        // We look in employees table, not users table
        $employee = Employee::where('email', $request->email)->first();

        // Step 3 — Always return success message even if email not found
        // Security best practice: never reveal if an email exists
        // Raw PHP equivalent: your intentional vague response
        if (!$employee || !$employee->hasAccount()) {
            return back()->with('success',
                'Si ce compte existe, un lien de réinitialisation 
                a été envoyé à votre adresse email.'
            );
        }

        // Step 4 — Send the reset link via Laravel's Password broker
        // This generates a token, stores it, and sends the notification
        $status = Password::sendResetLink(
            ['email' => $request->email]
        );

        return back()->with('success',
            'Si ce compte existe, un lien de réinitialisation 
            a été envoyé à votre adresse email.'
        );
    }

    /**
     * Handle password reset form submission.
     * Raw PHP equivalent: reset-password.php processing $_POST
     */
    public function resetPassword(Request $request)
    {
        // Step 1 — Validate input
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // Step 2 — Attempt the password reset
        // Laravel verifies: token exists, token not expired,
        // email matches token, then updates password automatically
        $status = Password::reset(
            $request->only([
                'email', 'password', 'password_confirmation', 'token'
            ]),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => \Illuminate\Support\Facades\Hash::make(
                                    $password
                                ),
                ])->save();

                // Update last_login timestamp
                $user->forceFill([
                    'last_login' => now(),
                ])->save();
            }
        );

        // Step 3 — Handle result
        if ($status === Password::PasswordReset) {
            return redirect()
                ->route('login')
                ->with('success',
                    'Mot de passe réinitialisé avec succès. 
                    Vous pouvez maintenant vous connecter.'
                );
        }

        // Step 4 — Reset failed (invalid/expired token)
        return back()->withErrors([
            'email' => 'Ce lien de réinitialisation est invalide ou expiré.'
        ]);
    }
}