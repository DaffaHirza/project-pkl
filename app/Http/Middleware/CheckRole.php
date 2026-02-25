<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * Usage: Route::middleware('role:admin') or Route::middleware('role:admin,superuser')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized($request, 'Silakan login terlebih dahulu.');
        }

        if (!$user->is_active) {
            auth()->guard()->logout();
            return $this->unauthorized($request, 'Akun Anda tidak aktif.');
        }

        // Superuser always has access
        if ($user->isSuperuser()) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!empty($roles) && !in_array($user->role, $roles)) {
            return $this->unauthorized($request, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }

    /**
     * Return unauthorized response
     */
    private function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', $message);
    }
}
