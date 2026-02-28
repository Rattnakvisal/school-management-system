<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TeacherLawRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LawRequestController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();
        $lawTypes = $this->lawTypes();

        $lawRequests = TeacherLawRequest::query()
            ->where('teacher_id', (int) $teacher->id)
            ->latest()
            ->get();

        return view('teacher.law-requests', [
            'lawTypes' => $lawTypes,
            'lawRequests' => $lawRequests,
        ]);
    }

    public function store(Request $request)
    {
        $teacher = $request->user();
        $lawTypes = $this->lawTypes();

        $validated = $request->validate([
            'law_type' => ['required', 'string', Rule::in(array_keys($lawTypes))],
            'subject' => ['required', 'string', 'max:150'],
            'requested_for' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);

        $lawRequest = TeacherLawRequest::query()->create([
            'teacher_id' => (int) $teacher->id,
            'law_type' => $validated['law_type'],
            'subject' => trim((string) $validated['subject']),
            'requested_for' => $validated['requested_for'] ?? null,
            'reason' => trim((string) $validated['reason']),
            'status' => 'pending',
        ]);

        Notification::query()->create([
            'type' => 'teacher_law_request',
            'title' => 'New teacher law request',
            'message' => $teacher->name . ' submitted a law request: ' . $lawRequest->subject,
            'url' => route('teacher.law-requests.index'),
            'is_read' => false,
        ]);

        return redirect()
            ->route('teacher.law-requests.index')
            ->with('success', 'Law request submitted successfully.');
    }

    private function lawTypes(): array
    {
        return [
            'school_policy' => 'School Policy',
            'classroom_rule' => 'Classroom Rule',
            'student_discipline' => 'Student Discipline',
            'safety' => 'Safety and Security',
            'other' => 'Other',
        ];
    }
}
