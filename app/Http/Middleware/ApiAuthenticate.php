<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $tokenRecord = ApiToken::where('token', hash('sha256', $plainToken))
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->with('user')
            ->first();

        if (! $tokenRecord || ! $tokenRecord->user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $tokenRecord->update(['last_used_at' => now()]);

        $user = $tokenRecord->user;
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
