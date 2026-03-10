<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LessonPlanController extends Controller
{
    /**
     * 1. TAMPILKAN DAFTAR MATERI (Untuk List Mentor)
     */
    public function index(Request $request)
    {
        $query = LessonPlan::with(['subject', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('scheduled_date', 'desc');

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
     * 2. TAMPILKAN DETAIL MATERI
     */
    public function show($id)
    {
        $lessonPlan = LessonPlan::with(['subject', 'user'])->find($id);

        if (!$lessonPlan) {
            return response()->json(['message' => 'Materi tidak ditemukan'], 404);
        }

        // Keamanan: Mentor hanya bisa lihat miliknya sendiri
        if (Auth::user()->role !== 'admin' && $lessonPlan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki akses ke materi ini'], 403);
        }

        return response()->json([
            'message' => 'Berhasil mengambil detail materi',
            'data'    => $lessonPlan
        ], 200);
    }

    /**
     * 3. SIMPAN MATERI BARU
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

    /**
     * 4. SINKRONISASI DROPDOWN (Punya kamu yang tadi)
     */
    public function getDropdown(Request $request)
    {
        $lessonPlans = LessonPlan::select('id', 'title')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil data materi untuk dropdown',
            'data'    => $lessonPlans
        ], 200);
    }

    /**
     * 5. TAMPILKAN JADWAL MENGAJAR (Global)
     */
    public function getSchedule(Request $request)
    {
        $schedules = LessonPlan::with(['subject', 'user'])
            ->orderBy('scheduled_date', 'asc')
            ->get();

        $formattedData = $schedules->groupBy('scheduled_date')->map(function ($plans, $date) {
            return [
                'date'     => $date,
                'day_name' => Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                'lessons'  => $plans->map(function ($plan) {
                    return [
                        'lesson_id'    => $plan->id,
                        'subject_name' => $plan->subject->name ?? 'Tanpa Bidang',
                        'topic'        => $plan->title,
                        'mentor_name'  => $plan->user->name ?? 'Tanpa Nama',
                        'mentor_role'  => $plan->user->role ?? 'guru',
                    ];
                })
            ];
        })->values();

        return response()->json([
            'message' => 'Berhasil mengambil jadwal mengajar',
            'data'    => $formattedData
        ], 200);
    }
}