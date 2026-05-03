<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user has the required role.
     *
     * Raw PHP equivalent:
     * if ($_SESSION['role'] !== 'Administrateur') {
     *     header('Location: /unauthorized');
     *     exit;
     * }
     *
     * Usage in routes:
     * Route::middleware(['auth', 'role:Administrateur'])
     * Route::middleware(['auth', 'role:Administrateur,Technicien'])
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        // Step 1 — Must be logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Step 2 — Must be active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()
                ->route('login')
                ->with('error',
                    'Votre compte a été désactivé. 
                     Contactez l\'administrateur.'
                );
        }

        // Step 3 — Check role
        // $roles is an array of allowed roles passed from the route
        // Example: role:Administrateur,Technicien
        // → $roles = ['Administrateur', 'Technicien']
        if (!empty($roles) && !in_array($user->role?->name, $roles)) {
            // User is logged in but doesn't have the required role
            abort(403, 'Accès refusé. Vous n\'avez pas les permissions requises.');
        }

        return $next($request);
    }
}