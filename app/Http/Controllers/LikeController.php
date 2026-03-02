<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Showcase; // Pastikan ini ada

class LikeController extends Controller
{
    public function toggleLike(Request $request, $showcase_id) // Ubah parameter jadi $showcase_id
    {
        // 1. Pastikan karyanya ada (kalau ID nggak ketemu, otomatis error 404)
        $showcase = Showcase::find($showcase_id); // Ganti findOrFail jadi find

        // Bikin pengecekan manual
        if (!$showcase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Waduh, karya (showcase) dengan ID tersebut tidak ditemukan.'
            ], 404);
        }

        // 2. Ambil ID user yang lagi login
        $userId = auth()->id();

        // 3. Cek apakah user ini sudah pernah nge-like karya ini?
        $existingLike = Like::where('user_id', $userId)
            ->where('showcase_id', $showcase_id) // <-- INI YANG BENAR: pakai showcase_id
            ->first();

        if ($existingLike) {
            // Kalau sudah nge-like -> Hapus Like-nya (Unlike)
            $existingLike->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Unlike karya ini',
                'is_liked' => false
            ]);
        } else {
            // Kalau belum nge-like -> Tambahkan Like baru
            Like::create([
                'user_id' => $userId,
                'showcase_id' => $showcase_id // <-- INI YANG BENAR: pakai showcase_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil Like karya ini',
                'is_liked' => true
            ]);
        }
    }
}
