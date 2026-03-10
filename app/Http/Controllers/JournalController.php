<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Attendances;
use App\Models\LessonPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    /**
     * TAHAP 1: Ambil data Siswa & Materi berdasarkan Bidang yang dipilih
     */
    public function getFormData(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $subjectId = $request->subject_id;

        // Ambil materi dari Planner yang sesuai bidang
        $materials = LessonPlan::where('subject_id', $subjectId)
            ->select('id', 'title', 'learning_objective', 'media_url', 'media_type')
            ->get();

        // Ambil siswa yang terdaftar di bidang ini
        // Asumsi: Siswa punya role 'student' dan kolom subject_id di tabel users
        $students = User::where('role', 'student')
            ->where('subject_id', $subjectId)
            ->select('id', 'name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'materials' => $materials,
                'students' => $students
            ]
        ]);
    }

    /**
     * TAHAP 2: Simpan Jurnal (Reguler / Penilaian)
     */
    public function store(Request $request)
    {
        // 1. Validasi Header Jurnal
        $rules = [
            'subject_id'     => 'required|exists:subjects,id',
            'lesson_plan_id' => 'required|exists:lesson_plans,id',
            'date'           => 'required|date',
            'session_type'   => 'required|in:Reguler,Penilaian',
            'notes'          => 'nullable|string',
            'image_proof'    => 'nullable|image|max:2048',
            'attendances'    => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
        ];

        // 2. Validasi Tambahan KHUSUS Sesi Penilaian (Sesuai UI kamu)
        if ($request->session_type === 'Penilaian') {
            $rules['attendances.*.competence_status'] = 'required|in:Tercapai,Proses,Pendampingan';
            $rules['attendances.*.showcase_status']   = 'required|in:Belum,Proses,Selesai';
            // Score opsional, tapi jika diisi harus angka
            $rules['attendances.*.score']             = 'nullable|numeric|min:0|max:100';
        }

        $request->validate($rules);

        return DB::transaction(function () use ($request) {
            $user = Auth::user();

            // Handle Upload Gambar
            $imagePath = null;
            if ($request->hasFile('image_proof')) {
                $imagePath = $request->file('image_proof')->store('journal_proofs', 'public');
            }

            // Simpan Header Jurnal
            $journal = Journal::create([
                'user_id'        => $user->id,
                'subject_id'     => $request->subject_id,
                'lesson_plan_id' => $request->lesson_plan_id,
                'date'           => $request->date,
                'session_type'   => $request->session_type,
                'notes'          => $request->notes,
                'image_proof'    => $imagePath,
                'is_verified'    => false,
            ]);

            // Simpan Detail Absensi/Penilaian
            foreach ($request->attendances as $att) {
                // Logika: Jika Reguler, paksa data penilaian jadi null
                $isPenilaian = ($request->session_type === 'Penilaian');

                Attendances::create([
                    'journal_id'               => $journal->id,
                    'student_id'               => $att['student_id'],
                    'status'                   => $att['status'] ?? 'hadir',

                    // Data ini hanya terisi jika Tipenya Penilaian
                    'competence_status'        => $isPenilaian ? $att['competence_status'] : null,
                    'showcase_status'          => $isPenilaian ? $att['showcase_status'] : null,
                    'is_competition_candidate' => $isPenilaian ? ($att['is_competition_candidate'] ?? false) : false,
                    'is_recovery_needed'       => $isPenilaian ? ($att['is_recovery_needed'] ?? false) : false,
                    'score'                    => $isPenilaian ? ($att['score'] ?? null) : null,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Jurnal ' . $request->session_type . ' berhasil disimpan!',
                'journal_id' => $journal->id
            ], 201);
        });
    }
}
