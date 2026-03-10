<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LessonPlanController; 
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/showcases', [ShowcaseController::class, 'index']);
Route::get('/showcases/{showcase_id}/comments', [CommentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user()->load(['subjects', 'studentProfile']);
    });

    // Route Umum (Auth Only)
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/user/select-subjects', [SubjectController::class, 'selfAssignSubject']);
    Route::get('/lesson-plans/dropdown', [LessonPlanController::class, 'getDropdown']);

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/journals', [AdminController::class, 'getAllJournals']);
        Route::patch('/admin/journals/{id}/verify', [AdminController::class, 'verifyJournal']);

        // User Management
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/admin/users', [AdminController::class, 'index']);
        Route::put('/admin/users/{id}', [AdminController::class, 'update']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroy']);
        Route::put('/admin/users/{id}/password', [AdminController::class, 'updatePassword']);
        Route::post('/admin/users/assign-subject', [AdminController::class, 'assignSubject']);

        // Subject Management
        Route::get('/admin/subjects/available-teachers', [SubjectController::class, 'availableTeachers']);
        Route::post('/admin/subjects/store', [SubjectController::class, 'store']);
        Route::put('/admin/subjects/{id}/update', [SubjectController::class, 'update']);
        Route::delete('/admin/subjects/{id}/delete', [SubjectController::class, 'destroy']);

        // Payroll
        Route::get('/admin/payroll-setting', [AdminController::class, 'getPayrollRate']);
        Route::post('/admin/payroll-setting/update', [AdminController::class, 'updatePayrollRate']);
        Route::get('/admin/payroll-dashboard', [AdminController::class, 'getPayrollDashboard']);
    });

    // Khusus Guru & Mentor
    Route::middleware('role:guru,mentor')->group(function () {
        Route::get('/lesson-plans', [LessonPlanController::class, 'index']);
        Route::post('/lesson-plans', [LessonPlanController::class, 'store']);
        Route::get('/lesson-plans/{id}', [LessonPlanController::class, 'show']);
        Route::get('/schedules', [LessonPlanController::class, 'getSchedule']);
    });

    // Khusus Siswa
    Route::middleware('role:siswa')->group(function () {
        Route::post('/showcases', [ShowcaseController::class, 'store']);
        Route::get('/my-showcases', [ShowcaseController::class, 'myShowcases']);
        Route::put('/showcases/{id}', [ShowcaseController::class, 'update']);
        Route::delete('/showcases/{id}', [ShowcaseController::class, 'destroy']);
        Route::post('/showcases/{showcase_id}/like', [LikeController::class, 'toggleLike']);
        Route::post('/showcases/{showcase_id}/comments', [CommentController::class, 'store']);
        Route::post('/profile', [ProfileController::class, 'update']);
    });

    // Profile (All Roles)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
});