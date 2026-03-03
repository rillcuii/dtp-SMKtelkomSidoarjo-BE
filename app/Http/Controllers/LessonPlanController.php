<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LessonPlanController extends Controller
{
    /**
     * 1. INPUT RENCANA (Monthly Planner) oleh Mentor
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'learning_objective' => 'required|string',
            'assessment_method' => 'required|string', // Sesuai dokumen
            'scheduled_date' => 'required|date',
            'scheduled_month' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Simpan data rencana bulanan
        $lessonPlan = LessonPlan::create([
            'user_id' => Auth::id(), // Otomatis ngambil ID mentor yang lagi login
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'learning_objective' => $request->learning_objective,
            'assessment_method' => $request->assessment_method,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_month' => $request->scheduled_month,
        ]);

        return response()->json([
            'message' => 'Rencana materi bulanan berhasil ditambahkan!',
            'data' => $lessonPlan
        ], 201);
    }

    /**
     * 2. SINKRONISASI DROPDOWN (Untuk Jurnal Harian)
     */
    public function getDropdown(Request $request)
    {
        // Hanya tarik planner milik mentor yang sedang login
        $lessonPlans = LessonPlan::select('id', 'title')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil data rencana materi untuk dropdown',
            'data' => $lessonPlans
        ], 200);
    }
}