<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LessonPlanController extends Controller
{
    /**
     * 1. TAMPILKAN DAFTAR MATERI (Sesuai UI List)
     */
    public function index(Request $request)
    {
        // PENTING: Kita pakai 'with' untuk narik data relasi Bidang (Subject) dan Pembuat (User)
        // supaya frontend bisa nampilin badge "Fiber Optic" dan nama "Pak Bagus"
        $query = LessonPlan::with(['subject', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('scheduled_date', 'desc'); // desc = Biar materi terbaru ada di paling atas

        // Opsional: Kalau UI butuh filter bulan
        if ($request->has('month')) {
            $query->where('scheduled_month', $request->month);
        }

        $lessonPlans = $query->get();

        return response()->json([
            'message' => 'Berhasil mengambil daftar materi',
            'data'    => $lessonPlans
        ], 200);
    }

    /**
     * 2. TAMPILKAN DETAIL MATERI (Halaman Preview Video/PDF - Rickroll Page)
     */
    public function show($id)
    {
        // Cari materi berdasarkan ID, bawa juga data subject dan usernya
        $lessonPlan = LessonPlan::with(['subject', 'user'])->find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Materi tidak ditemukan'], 404);
        }

        // Keamanan: Pastikan hanya mentor pembuatnya yang bisa buka (atau Admin nantinya)
        if ($lessonPlan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke materi ini'], 403);
        }

        return response()->json([
            'message' => 'Berhasil mengambil detail materi',
            'data'    => $lessonPlan
        ], 200);
    }

    /**
     * 3. INPUT RENCANA (Sesuai UI Form Step 1, 2, 3)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id'         => 'required|exists:subjects,id',
            'title'              => 'required|string|max:255',
            'learning_objective' => 'required|string',
            'scheduled_date'     => 'required|date',
            'media_type'         => 'nullable|string',
            'media_url'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $month = date('m', strtotime($request->scheduled_date));

        $lessonPlan = LessonPlan::create([
            'user_id'            => Auth::id(),
            'subject_id'         => $request->subject_id,
            'title'              => $request->title,
            'learning_objective' => $request->learning_objective,
            'media_type'         => $request->media_type,
            'media_url'          => $request->media_url,
            'scheduled_date'     => $request->scheduled_date,
            'scheduled_month'    => $month,
        ]);

        return response()->json([
            'message' => 'Materi berhasil disimpan!',
            'data'    => $lessonPlan
        ], 201);
    }
}
