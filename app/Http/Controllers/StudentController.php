<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function add_student(Request $request, $classId)
    {
        $request->validate([
            'student_id' => 'nullable|exists:users,id',
            'firstname' => 'required_without:student_id|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required_without:student_id|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        $class = Classes::findOrFail($classId);

        if ($request->student_id) {
            // Attach existing student
            $studentId = $request->student_id;
        } else {
            // Create a new student
            $student = User::create([
                'firstname' => $request->firstname,
                'middlename' => $request->middlename ?? null,
                'lastname'  => $request->lastname,
                'username'  => $request->username ?? null,
                'email'     => $request->email ?? null,
                'password'  => isset($request->password) ? Hash::make($request->password) : Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            // Assign student role
            $student->assignRole('student');

            $studentId = $student->id;
        }

        // Attach student to class without detaching existing ones
        $class->students()->syncWithoutDetaching([$studentId]);

        // Return JSON response for Axios
        return response()->json([
            'student' => [
                'id' => $student->id,
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'username' => $student->username,
                'email' => $student->email,
            ]
        ]);

    }

public function teachers()
{
    $teachers = User::role('teacher')
        ->select(
            'id',
            'firstname',
            'middlename',
            'lastname',
            'username',
            'email'
        )
        ->orderBy('lastname')
        ->get()
        ->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'fullname' => trim(
                    "{$teacher->firstname} {$teacher->middlename} {$teacher->lastname}"
                ),
                'username' => $teacher->username,
                'email' => $teacher->email,
            ];
        });

    return Inertia::render('Teacher/Teachers', [
        'teachers' => $teachers,
    ]);

    
}

public function store_teacher(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname'  => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username',
            'email'     => 'nullable|email|unique:users,email',
            'password'  => 'nullable|string|min:6',
        ]);

        $teacher = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $teacher->assignRole('teacher');

        return redirect()->route('teacher.teachers')
            ->with('success', 'Teacher added successfully.');
    }

    public function update_student(Request $request, User $student)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $student->id,
            'email' => 'nullable|email|unique:users,email,' . $student->id,
            'password' => 'nullable|string|min:6',
        ]);

        $student->update([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $student->password,
        ]);

        return response()->json(['student' => $student], 200);
    }

    // Delete student
    public function delete_student(User $student)
    {
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully'], 200);
    }

}
