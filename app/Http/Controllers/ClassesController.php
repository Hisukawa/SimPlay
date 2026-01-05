<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClassesController extends Controller
{
    // ---------------- LIST CLASSES ----------------
    public function classes()
    {
        $classes = Classes::withCount(['students as student_count' => function ($query) {
            $query->role('student');
        }])
        ->with(['students' => function ($query) {
            $query->role('student');
        }])
        ->where('teacher_id', Auth::id())
        ->get();

        return Inertia::render('Teacher/Classes', [
            'initialClasses' => $classes,
        ]);
    }

    // ---------------- ADD CLASS ----------------
    public function add_class(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $class = Classes::create([
            'name' => $request->name,
            'teacher_id' => Auth::id(),
        ]);

        // If using React axios, return JSON instead of redirect
        return response()->json(['class' => $class], 201);
    }

    // ---------------- UPDATE CLASS ----------------
    public function update_class(Request $request, Classes $class)
    {
        // Optional: ensure class belongs to current teacher
        if ($class->teacher_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $class->update([
            'name' => $request->name,
        ]);

        return response()->json(['class' => $class], 200);
    }

    // ---------------- DELETE CLASS ----------------
    public function delete_class(Classes $class)
    {
        // Optional: ensure class belongs to current teacher
        if ($class->teacher_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $class->delete();

        return response()->json(['message' => 'Class deleted successfully'], 200);
    }
}
