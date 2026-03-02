<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * parameter ...$roles memungkinkan kita mengecek lebih dari satu role sekaligus.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // PENTING: Gunakan $request->user() agar bisa membaca Bearer Token dari Sanctum
        $user = $request->user();

        // 1. Cek apakah ada user (token valid)
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Silakan login terlebih dahulu.'
            ], 401);
        }

        // 2. Cek apakah rolenya sesuai
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses terlarang. Role Anda tidak sesuai.'
            ], 403);
        }

        return $next($request);
    }
}
