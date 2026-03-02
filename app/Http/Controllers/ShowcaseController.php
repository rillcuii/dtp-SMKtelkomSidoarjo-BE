<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Showcase;
use Illuminate\Support\Facades\Auth;

class ShowcaseController extends Controller
{
    /**
     * 1. GET Semua Karya (Untuk Halaman Galeri Sekolah)
     */
    public function index()
    {
        // Tambahkan 'tags' ke dalam array with() biar ikut tampil
        $showcases = Showcase::with(['user:id,name', 'urls', 'tags'])
            ->withCount(['likes', 'comments']) // (Kalau pakai fitur hitung otomatis)
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $showcases]);
    }

    /**
     * 2. GET Karya Saya (Untuk Halaman Kelola Portofolio)
     */
    public function myShowcases()
    {
        // Tambahkan 'tags' juga di sini
        $showcases = Showcase::with(['urls', 'tags'])
            ->where('student_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $showcases
        ]);
    }

    /**
     * 3. POST Upload Karya Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'media_url' => 'required|url',
            'media_type' => 'required|in:image,video',

            // Validasi format URL dinamis
            'urls' => 'nullable|array',
            'urls.*.name' => 'required_with:urls|string',
            'urls.*.url' => 'required_with:urls|url',

            // Validasi format Tags dinamis (Sama persis kayak URL)
            'tags' => 'nullable|array',
            'tags.*.name' => 'required_with:tags|string',
        ]);

        // Simpan data utama ke tabel showcases
        $showcase = Showcase::create([
            'student_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'media_url' => $request->media_url,
            'media_type' => $request->media_type,
        ]);

        // Simpan URL ke tabel showcase_urls
        if ($request->has('urls') && count($request->urls) > 0) {
            $showcase->urls()->createMany($request->urls);
        }

        // Simpan Tags ke tabel showcase_tags
        if ($request->has('tags') && count($request->tags) > 0) {
            $showcase->tags()->createMany($request->tags);
        }

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil diunggah!',
            'data' => $showcase->load(['urls', 'tags']) // Load relasi urls & tags untuk response
        ], 201);
    }

    /**
     * 4. PUT Edit Karya
     */
    public function update(Request $request, $id)
    {
        $showcase = Showcase::where('id', $id)->where('student_id', Auth::id())->first();

        if (!$showcase) {
            return response()->json(['message' => 'Karya tidak ditemukan atau bukan milik Anda'], 404);
        }

        // Update data utama showcase
        $showcase->update($request->only([
            'title',
            'description',
            'media_url',
            'media_type'
        ]));

        // Update URL
        if ($request->has('urls')) {
            $showcase->urls()->delete();
            if (count($request->urls) > 0) {
                $showcase->urls()->createMany($request->urls);
            }
        }

        // Update Tags (Konsepnya sama: hapus yang lama, masukin yang baru)
        if ($request->has('tags')) {
            $showcase->tags()->delete();
            if (count($request->tags) > 0) {
                $showcase->tags()->createMany($request->tags);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil diperbarui!',
            'data' => $showcase->load(['urls', 'tags'])
        ]);
    }

    /**
     * 5. DELETE Hapus Karya
     */
    public function destroy($id)
    {
        $showcase = Showcase::where('id', $id)->where('student_id', Auth::id())->first();

        if (!$showcase) {
            return response()->json(['message' => 'Karya tidak ditemukan atau bukan milik Anda'], 404);
        }

        // URL dan Tags otomatis kehapus karena cascade di migration
        $showcase->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil dihapus!'
        ]);
    }
}
