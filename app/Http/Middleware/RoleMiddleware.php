<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            $method = match (strtolower($role)) {
                'admin' => 'isAdmin',
                'manager' => 'isManager',
                'receptionist' => 'isReceptionist',
                'chef' => 'isChef',
                'owner' => 'isAdmin',
                default => 'hasRole',
            };

            if ($method === 'hasRole' ? $user->hasRole($role) : $user->{$method}()) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized: you do not have access to this area.');
    }
}
