<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request - allows admin and superuser only.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized($request, 'Silakan login terlebih dahulu.');
        }

        if (!$user->is_active) {
            auth()->guard()->logout();
            return $this->unauthorized($request, 'Akun Anda tidak aktif.');
        }

        if (!$user->isAdmin() && !$user->isSuperuser()) {
            return $this->unauthorized($request, 'Halaman ini hanya untuk admin.');
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
