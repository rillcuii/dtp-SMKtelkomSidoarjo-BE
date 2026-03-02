<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    // List semua bidang
    public function index()
    {
        $subjects = Subject::all();
        return response()->json([
            'success' => true,
            'data'    => $subjects
        ]);
    }

    // Simpan Bidang Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:subjects,name',
            'description' => 'nullable'
        ]);

        $subject = Subject::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidang berhasil dibuat!',
            'data'    => $subject
        ], 201);
    }

    // Ambil detail satu bidang
    public function show($id)
    {
        $subject = Subject::find($id);
        if (!$subject) {
            return response()->json(['message' => 'Bidang tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $subject]);
    }

    // Update Bidang
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:subjects,name,' . $id,
        ]);

        $subject->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidang berhasil diupdate!',
            'data'    => $subject
        ]);
    }

    // Hapus Bidang
    public function destroy($id)
    {
        $subject = Subject::find($id);
        if (!$subject) {
            return response()->json(['message' => 'Bidang tidak ditemukan'], 404);
        }
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bidang berhasil dihapus'
        ]);
    }

    public function selfAssignSubject(Request $request)
    {
        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $user = auth()->user(); // Ambil user dari token login
        $user->subjects()->sync($request->subject_ids);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memilih bidang!',
            'data' => $user->load('subjects')
        ]);
    }
}
