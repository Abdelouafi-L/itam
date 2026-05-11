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
     * Now uses Spatie's hasRole() method.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()
                ->route('login')
                ->with('error',
                    'Votre compte a été désactivé. ' .
                    'Contactez l\'administrateur.'
                );
        }

        // Spatie's hasRole() accepts array — check if user has
        // any of the allowed roles
        if (!empty($roles) && !$user->hasAnyRole($roles)) {
            abort(403,
                'Accès refusé. Vous n\'avez pas les permissions requises.'
            );
        }

        return $next($request);
    }
}