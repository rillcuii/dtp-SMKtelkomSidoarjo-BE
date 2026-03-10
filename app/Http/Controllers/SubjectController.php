<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    // 1. LIHAT SEMUA (LIST BIDANG + MENTOR + SISWA)
public function index()
{
    // Eager load users (tarik sekali jalan supaya laju)
    $subjects = Subject::with('users')->get();

    $data = $subjects->map(function ($subject) {
        return [
            'id' => $subject->id,
            'name' => $subject->name,
            'description' => $subject->description,
            'teacher_initials' => $subject->teacher_initials, 
            // Filter Mentor/Guru
            'teachers' => $subject->users->whereIn('role', ['mentor', 'guru'])->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role
            ])->values(),
            // Filter Siswa
            // 'students' => $subject->users->where('role', 'siswa')->map(fn($s) => [
            //     'id' => $s->id,
            //     'name' => $s->name
            // ])->values(),
            // 'total_students' => $subject->users->where('role', 'siswa')->count()
        ];
    });

    return response()->json(['success' => true, 'data' => $data]);
}

// 2. STORE (TAMBAH BIDANG)
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|unique:subjects,name', // Tak boleh ada nama bidang sama
        'description' => 'nullable|string',
        'teacher_ids' => 'nullable|array',
        'teacher_ids.*' => 'exists:users,id'
    ]);

    $subject = Subject::create([
        'name' => $request->name,
        'slug' => \Illuminate\Support\Str::slug($request->name),
        'description' => $request->description,
    ]);

    if ($request->has('teacher_ids')) {
        $subject->users()->sync($request->teacher_ids);
    }

    return response()->json(['success' => true, 'message' => 'Bidang berhasil dibuat!']);
}

// 3. UPDATE (EDIT BIDANG & TUKAR MENTOR)
public function update(Request $request, $id)
{
    $subject = Subject::findOrFail($id);

    $request->validate([
        'name' => 'required|unique:subjects,name,' . $id,
        'teacher_ids' => 'nullable|array',
        'teacher_ids.*' => 'exists:users,id', // <--- TAMBAHKAN INI
    ]);

    $subject->update([
        'name' => $request->name,
        'slug' => \Illuminate\Support\Str::slug($request->name),
        'description' => $request->description,
    ]);

    if ($request->has('teacher_ids')) {
        $subject->users()->sync($request->teacher_ids);
    }

    return response()->json(['success' => true, 'message' => 'Data bidang & mentor berhasil diperbarui!']);
}

public function availableTeachers()
{
    $teachers = User::whereIn('role', ['mentor', 'guru'])->get(['id', 'name', 'role']);
    return response()->json(['success' => true, 'data' => $teachers]);
}

// 4. DELETE (HAPUS BIDANG)
public function destroy($id)
{
    // Cari bidangnya, kalau nggak ada langsung error 404
    $subject = Subject::findOrFail($id);

    // Putus hubungan bidang ini dengan siapapun (mentor/siswa) di tabel pivot
    // Ini penting supaya nggak ada data "yatim piatu" di tabel subject_user
    $subject->users()->detach();

    // Baru hapus bidangnya
    $subject->delete();

    return response()->json([
        'success' => true, 
        'message' => 'Bidang berhasil dihapus!'
    ]);
}
    
}