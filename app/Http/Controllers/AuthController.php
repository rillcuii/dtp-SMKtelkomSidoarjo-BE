<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input dari Front-End
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cek apakah email dan password cocok di database
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah!'
            ], 401);
        }

        // 3. Kalau cocok, ambil data usernya dan buatkan Token
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Kirim response ke Front-End
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function register(Request $request)
    {
        // 1. Validasi Input (Hanya name, email, dan role)
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role'  => 'required|in:siswa,guru,mentor',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                // 2. Buat User dengan Password Default '12345678'
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make('12345678'), // Password otomatis set
                    'role'     => $request->role,
                ]);

                // 3. Logika khusus Siswa: Buat Profile Otomatis (Kosong)
                if ($user->role === 'siswa') {
                    $user->studentProfile()->create();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Akun ' . ucfirst($user->role) . ' berhasil dibuat oleh Admin!',
                    'data'    => [
                        'user' => $user->load('studentProfile'),
                        'default_password' => '12345678' // Info buat admin
                    ]
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akun: ' . $e->getMessage()
            ], 500);
        }
    }
}
