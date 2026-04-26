<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        if ($student) {
            $student->loadMissing('majorSubject');

            if (Schema::hasTable('student_major_subjects')) {
                $student->loadMissing('majorSubjects');
            }
        }

        return view('student.settings');
    }

    public function updatePassword(Request $request)
    {
        $student = $request->user();

        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $student->password = (string) $validated['password'];
        $student->save();

        return redirect()
            ->route('student.settings')
            ->with('success', 'Password updated successfully.');
    }
}
