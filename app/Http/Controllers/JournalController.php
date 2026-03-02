<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonPlan;
use App\Models\Journal;

class JournalController extends Controller
{
    /**
     * 1. API untuk mengisi Dropdown "Materi" di Front-End
     */
    public function getLessonPlans(Request $request)
    {
        // Cari tahu Mentor ini ngajar pelajaran apa aja dari Tokennya
        $mySubjectIds = $request->user()->subjects->pluck('id');

        // Tarik data Rencana Pembelajaran (Materi) khusus buat pelajaran dia doang
        $lessonPlans = LessonPlan::whereIn('subject_id', $mySubjectIds)
            ->with('subject:id,name') // Tempelin nama mapelnya biar FE gampang ngebacanya
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data materi berhasil diambil',
            'data' => $lessonPlans
        ]);
    }

    /**
     * 2. API untuk menerima form Submit Jurnal dari Front-End
     */
    public function store(Request $request)
    {
        // Validasi inputan dari FE harus lengkap dan sesuai aturan
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'lesson_plan_id' => 'required|exists:lesson_plans,id',
            'date' => 'required|date',
            'status' => 'required|in:merah,kuning,hijau', // Sesuai warna tombol di Demo FE!
            'description' => 'required|string',
            'material_link' => 'nullable|url' // Boleh kosong, tapi kalau diisi harus format link (http...)
        ]);

        // Simpan ke database
        $journal = Journal::create([
            'user_id' => $request->user()->id, // Otomatis pakai ID mentor yang lagi login
            'subject_id' => $request->subject_id,
            'lesson_plan_id' => $request->lesson_plan_id,
            'date' => $request->date,
            'status' => $request->status,
            'description' => $request->description,
            'material_link' => $request->material_link,
            'is_verified' => false, // Default false, nunggu Admin verifikasi buat Payroll (Sesuai PRD)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil disubmit dalam < 20 detik!',
            'data' => $journal
        ], 201); // 201 Created
    }
}
