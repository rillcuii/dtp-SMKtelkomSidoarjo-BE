<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Showcase;

class CommentController extends Controller
{
    // --- 1. MENGAMBIL DAFTAR KOMENTAR ---
    public function index($showcase_id)
    {
        // Cek dulu karyanya ada atau nggak pakai find() biar santai
        $showcase = Showcase::find($showcase_id);

        if (!$showcase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karya (showcase) tidak ditemukan.'
            ], 404);
        }

        // Ambil komentar beserta nama user yang komen, dan balasan komentarnya (replies)
        $comments = Comment::with(['user:id,name', 'replies.user:id,name'])
            ->where('showcase_id', $showcase_id)
            ->whereNull('parent_id') // Hanya ambil komentar utama (bukan balasan)
            ->latest() // Urutkan dari yang paling baru
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $comments
        ]);
    }

    // --- 2. MENAMBAH KOMENTAR BARU ---
    public function store(Request $request, $showcase_id) // <-- Pakai $showcase_id
    {
        // 1. Validasi inputan dari Frontend
        $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id' // Validasi kalau ini reply
        ]);

        // Cek karyanya ada atau nggak (Pakai find biar nggak nembak error 404 default Laravel)
        $showcase = Showcase::find($showcase_id);

        if (!$showcase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karya (showcase) tidak ditemukan.'
            ], 404);
        }

        $user = auth()->user();

        // 2. THE MAGIC: Cek apakah yang komen ini Mentor?
        // Logic buatanmu udah PERFECT! 💯
        $isOfficial = ($user->role === 'mentor' || $user->role === 'guru') ? true : false;

        // 3. Simpan komentar ke database
        $comment = Comment::create([
            'user_id' => $user->id,
            'showcase_id' => $showcase->id, // <-- Pakai showcase_id
            'body' => $request->body,
            'parent_id' => $request->parent_id,
            'is_official_review' => $isOfficial
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil ditambahkan',
            'data' => $comment
        ], 201); // 201 itu status code untuk "Created"
    }
}
