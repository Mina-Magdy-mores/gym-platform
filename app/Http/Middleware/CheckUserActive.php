<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request for frozen/inactive accounts across Web & Mobile APIs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user()->fresh();

            // If user is frozen (is_active = false) and trying to perform any state-changing action (POST, PUT, PATCH, DELETE)
            if ($user && !$user->is_active && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $message = 'Your account is currently frozen/inactive. Please contact gym administration to activate your account.';

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => $message,
                    ], 403);
                }

                return redirect()->back()->with('error', $message);
            }
        }

        return $next($request);
    }
}
