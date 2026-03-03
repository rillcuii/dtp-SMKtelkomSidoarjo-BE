<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonPlanController;

Route::post('/login', [AuthController::class, 'login']);

//lihat showcase
Route::get('/showcases', [ShowcaseController::class, 'index']);

//lihat comment
Route::get('/showcases/{showcase_id}/comments', [CommentController::class, 'index']);

// Wajib Login dlu
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user()->load(['subjects', 'studentProfile']);
    });

    Route::post('/lesson-plans', [LessonPlanController::class, 'store']);
    Route::get('/lesson-plans/dropdown', [LessonPlanController::class, 'getDropdown']);

    Route::post('/showcases/{showcase_id}/comments', [CommentController::class, 'store']);

    Route::get('/profile', [ProfileController::class, 'show']);           // Lihat profil sendiri
    Route::put('/profile', [ProfileController::class, 'update']);         // Update nama/bio sendiri 
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']); // Ganti password 

    Route::post('/user/select-subjects', [SubjectController::class, 'selfAssignSubject']);
    Route::get('/subjects', [SubjectController::class, 'index']);

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        // Lihat semua list jurnal
        Route::get('/admin/journals', [AdminController::class, 'getAllJournals']);

        // Verifikasi jurnal tertentu
        Route::patch('/admin/journals/{id}/verify', [AdminController::class, 'verifyJournal']);

        // USER MANAGEMENT
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/admin/users', [AdminController::class, 'index']);           // List semua user
        Route::put('/admin/users/{id}', [AdminController::class, 'update']);     // Edit user lain
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroy']); // Hapus user lain
        Route::put('/admin/users/{id}/password', [AdminController::class, 'updatePassword']); //admin ganti password user lain
        Route::post('/admin/users/assign-subject', [AdminController::class, 'assignSubject']);

        // bidang management
        Route::get('/admin/subjects', [SubjectController::class, 'index']);           // List semua bidang
        Route::post('/admin/subjects/store', [SubjectController::class, 'store']);    // Tambah bidang baru
        Route::get('/admin/subjects/{id}', [SubjectController::class, 'show']);       // Detail bidang
        Route::put('/admin/subjects/{id}/update', [SubjectController::class, 'update']); // Update bidang
        Route::delete('/admin/subjects/{id}/delete', [SubjectController::class, 'destroy']); // Hapus bidang

        // PAYROLL SETTING
        Route::get('/admin/payroll-setting', [AdminController::class, 'getPayrollRate']);
        Route::post('/admin/payroll-setting/update', [AdminController::class, 'updatePayrollRate']);
        Route::get('/admin/payroll-dashboard', [AdminController::class, 'getPayrollDashboard']);
    });

    //Khusus Guru & Mentor
    Route::middleware('role:guru,mentor')->group(function () {
        // Tarik data buat dropdown materi
        Route::get('/lesson-plans', [JournalController::class, 'getLessonPlans']);

        // Kirim/Submit form jurnal
        Route::post('/journals', [JournalController::class, 'store']);
    });

    //khusus mentor
    // Route::middleware('role:mentor')->group(function () {

    // });

    // 3. Khusus Siswa (Bisa upload karya ke Showcase)
    Route::middleware('role:siswa')->group(function () {
        // API SHOWCASE (Project Saya)
        Route::post('/showcases', [ShowcaseController::class, 'store']); // Upload karya
        Route::get('/my-showcases', [ShowcaseController::class, 'myShowcases']); // Lihat karya sendiri 
        Route::put('/showcases/{id}', [ShowcaseController::class, 'update']); // Update karya
        Route::delete('/showcases/{id}', [ShowcaseController::class, 'destroy']); // Hapus karya                
        Route::post('/showcases/{showcase_id}/like', [LikeController::class, 'toggleLike']); // Like/Unlike karya       

        // API PROFIL SISWA
        Route::post('/profile', [ProfileController::class, 'update']); // Simpan/Update profil
    });
});
