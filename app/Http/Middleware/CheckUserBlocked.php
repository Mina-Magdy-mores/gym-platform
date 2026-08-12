<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBlocked
{
    /**
     * Handle an incoming request for Web & Mobile APIs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Read fresh status directly from database to catch immediate admin blocks
            $user = Auth::user()->fresh();

            if ($user && $user->is_blocked) {
                // Instantly revoke active Sanctum API tokens for Mobile App
                if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }

                $reason = $user->block_reason ? " Reason: {$user->block_reason}" : '';
                $errorMessage = "Your account has been suspended by administration.{$reason}";

                // Handle JSON API Requests for Mobile App
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => $errorMessage,
                    ], 403);
                }

                // Handle Web Requests
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => $errorMessage,
                ]);
            }
        }

        return $next($request);
    }
}
