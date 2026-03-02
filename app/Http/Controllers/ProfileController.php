<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * GET: Ambil data profil (Siswa/Guru/Mentor)
     */
    public function show()
    {
        $user = Auth::user()->load('studentProfile');

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * UPDATE INFO PROFIL & NAMA
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'    => 'required|string|max:255', // Nama user ada di tabel users
            'kelas'   => 'nullable|string|max:50',
            'jurusan' => 'nullable|string|max:100',
            'bio'     => 'nullable|string',
            'skills'  => 'nullable|string',
        ]);

        // 1. Update Nama di tabel 'users'
        $user->update(['name' => $request->name]);

        // 2. Update/Create detail di tabel 'student_profiles' (Hanya jika Siswa)
        if ($user->role === 'siswa') {
            $user->studentProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['kelas', 'jurusan', 'bio', 'skills'])
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user->load('studentProfile')
        ]);
    }

    /**
     * GANTI PASSWORD
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Cek apakah password lama benar
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama salah.'
            ], 422);
        }

        // Simpan password baru
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diganti!'
        ]);
    }
}
