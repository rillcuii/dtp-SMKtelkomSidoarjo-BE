<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Setting;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Melihat semua jurnal yang masuk dari semua mentor
     */
    public function getAllJournals()
    {
        $journals = Journal::with(['user:id,name', 'subject:id,name', 'lessonPlan:id,title'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $journals
        ]);
    }

    /**
     * Verifikasi Jurnal (Tombol ACC Admin)
     */
    public function verifyJournal($id)
    {
        $journal = Journal::find($id);

        if (!$journal) {
            return response()->json(['message' => 'Jurnal tidak ditemukan'], 404);
        }

        $journal->update(['is_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil diverifikasi untuk payroll!',
            'data' => $journal
        ]);
    }

    public function index()
    {
        // Ambil semua user beserta profil siswanya (kalau ada)
        $users = User::with('studentProfile', 'subjects')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * 2. Admin Edit Data User (Nama, Email, Role)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:siswa,guru,mentor',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        // Kalau Admin ganti role user jadi 'siswa', pastiin dia punya row di student_profiles
        if ($user->role === 'siswa' && !$user->studentProfile) {
            $user->studentProfile()->create();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui oleh Admin',
            'data' => $user->load('studentProfile')
        ]);
    }

    /**
     * 3. Admin Hapus User
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Hapus profil siswanya dulu kalau ada biar nggak error constraint
        if ($user->studentProfile) {
            $user->studentProfile()->delete();
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus selamanya!'
        ]);
    }

    /**
     * 4. ADMIN RESET PASSWORD USER LAIN
     */
    public function updatePassword(Request $request, $id)
    {
        // Validasi: Minimal 8 karakter dan harus ada konfirmasi
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);

        // Update password tanpa ngecek password lama (karena ini akses Admin)
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password user ' . $user->name . ' berhasil direset oleh Admin!'
        ]);
    }

    public function assignSubject(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->subjects()->sync($request->subject_ids);

        return response()->json([
            'success' => true,
            'message' => 'Mapping bidang untuk ' . $user->name . ' berhasil diperbarui!',
            'data' => $user->load('subjects')
        ]);
    }

    /**
     * Mengambil harga honor saat ini
     */
    public function getPayrollRate()
    {
        $rate = Setting::where('key', 'hourly_rate')->first();
        return response()->json([
            'success' => true,
            'hourly_rate' => $rate ? $rate->value : 0
        ]);
    }

    /**
     * Update harga honor (Setting Payroll)
     */
    public function updatePayrollRate(Request $request)
    {
        $request->validate([
            'hourly_rate' => 'required|numeric|min:0'
        ]);

        // updateOrCreate: Kalau belum ada datanya dibikinin, kalau sudah ada diupdate
        $setting = Setting::updateOrCreate(
            ['key' => 'hourly_rate'],
            ['value' => $request->hourly_rate]
        );

        return response()->json([
            'success' => true,
            'message' => 'Harga honor per sesi berhasil diperbarui!',
            'data' => $setting
        ]);
    }

    /**
     * Mengambil Ringkasan Payroll untuk Dashboard Admin
     */
    public function getPayrollDashboard()
    {
        // 1. Ambil Rate Aktif dari tabel settings
        $rateSetting = \App\Models\Setting::where('key', 'hourly_rate')->first();
        $hourlyRate = $rateSetting ? (int)$rateSetting->value : 0;

        // 2. Ambil rincian per mentor (Hanya yang jurnalnya sudah diverifikasi)
        // Kita kelompokkan berdasarkan user_id
        $mentorPayments = \App\Models\Journal::where('is_verified', true)
            ->with('user:id,name,role')
            ->select('user_id', DB::raw('count(*) as total_sesi'))
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) use ($hourlyRate) {
                return [
                    'nama_mentor' => $item->user->name,
                    'role'        => $item->user->role,
                    'jml_sesi'    => $item->total_sesi . ' Sesi',
                    'sub_total'   => $item->total_sesi * $hourlyRate
                ];
            });

        // 3. Hitung Total Keseluruhan
        $totalSesi = $mentorPayments->sum(function ($item) {
            return (int) filter_var($item['jml_sesi'], FILTER_SANITIZE_NUMBER_INT);
        });
        $totalHonor = $totalSesi * $hourlyRate;

        return response()->json([
            'success' => true,
            'data' => [
                'total_honor'       => $totalHonor,
                'total_sesi_verified' => $totalSesi,
                'rate_aktif'        => $hourlyRate,
                'rincian_mentor'    => $mentorPayments
            ]
        ]);
    }
}
