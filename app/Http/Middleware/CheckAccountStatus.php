<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request - check if user account is active.
     * Logout user if account has been deactivated.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is not authenticated, continue
        if (!$user) {
            return $next($request);
        }

        // Check if user account is still active
        if (!$user->is_active) {
            auth()->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin.');
        }

        return $next($request);
    }
}
