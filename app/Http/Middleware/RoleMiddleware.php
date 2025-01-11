<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure the user's role matches the required role (by name or ID)
        $userRole = $user->role; // Assuming a relationship exists in User model
        if (!$userRole || ($userRole->name !== $role && $userRole->id != $role)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
