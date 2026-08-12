<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->is_blocked) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $reason = $user->block_reason ? " Reason: {$user->block_reason}" : '';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Your account has been suspended by administration.{$reason}",
                    ], 403);
                }

                return redirect()->route('login')->withErrors([
                    'email' => "Your account has been suspended by administration.{$reason}",
                ]);
            }
        }

        return $next($request);
    }
}
