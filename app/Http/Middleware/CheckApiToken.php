<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiToken;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for bearer token in header
        $token = $request->bearerToken();
        
        // If no bearer token, check if user is authenticated via session (web auth)
        if (!$token && auth()->check()) {
            return $next($request);
        }
        
        // Validate API token
        if ($token) {
            $apiToken = ApiToken::where('token', hash('sha256', $token))
                ->where('expires_at', '>', now())
                ->first();
            
            if ($apiToken) {
                // Set the user for this request
                $user = $apiToken->user;
                if ($user) {
                    auth()->setUser($user);
                    return $next($request);
                }
            }
        }
        
        // For API requests, return JSON error
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // For web requests, redirect to login
        return redirect('/login');
    }
}
