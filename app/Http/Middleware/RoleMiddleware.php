<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();
        
        \Log::info('Role Middleware Check', [
            'user_id' => $user ? $user->id : null,
            'user_role' => $user ? $user->role : 'no user',
            'required_roles' => $roles,
            'is_authenticated' => auth()->check(),
            'request_path' => $request->path(),
            'request_method' => $request->method()
        ]);

        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Convert roles to lowercase for case-insensitive comparison
        $userRole = strtolower($user->role);
        $allowedRoles = array_map('strtolower', $roles);

        // Check if user has any of the required roles
        if (!in_array($userRole, $allowedRoles)) {
            \Log::warning('Role Mismatch', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_roles' => $roles
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Insufficient permissions.',
                'debug' => [
                    'user_role' => $user->role,
                    'required_roles' => $roles
                ]
            ], 403);
        }

        return $next($request);
    }
}
