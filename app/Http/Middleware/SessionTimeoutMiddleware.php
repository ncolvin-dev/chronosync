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
        try {
            if (!auth()->check()) {
                return $next($request);
            }

            $lastActivity = session(self::LAST_ACTIVITY_KEY);
            $now = time();

            if ($lastActivity && ($now - $lastActivity) > (self::TIMEOUT_MINUTES * 60)) {
                return $this->forceLogout();
            }

            session([self::LAST_ACTIVITY_KEY => $now]);

            return $next($request);
        } catch (\Throwable $e) {
            return $this->forceLogout();
        }
    }

    private function forceLogout(): Response
    {
        try {
            auth()->logout();
            session()->flush();
            session()->regenerate();
        } catch (\Throwable) {
            // Session may already be invalid — ignore and redirect anyway
        }

        return redirect()->route('login')
            ->with('warning', 'Your session has expired. Please log in again.');
    }
}
