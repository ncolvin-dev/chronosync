<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    /**
     * The inactivity timeout in minutes.
     */
    private const TIMEOUT_MINUTES = 60;

    /**
     * Session key for tracking last activity.
     */
    private const LAST_ACTIVITY_KEY = 'last_activity';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $lastActivity = session(self::LAST_ACTIVITY_KEY);
        $now = time();

        // Check if session has timed out
        if ($lastActivity && ($now - $lastActivity) > (self::TIMEOUT_MINUTES * 60)) {
            auth()->logout();
            session()->flush();

            return redirect()->route('login')
                ->with('warning', 'Your session has expired due to inactivity. Please log in again.');
        }

        // Update last activity time
        session([self::LAST_ACTIVITY_KEY => $now]);

        return $next($request);
    }
}
