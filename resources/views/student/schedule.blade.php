@extends('layout.students.navbar')

@section('page')
    <div class="student-stage space-y-6">
        <section class="student-reveal student-float admin-page-header" style="--sd: 1;">
            <div>
                <div class="admin-page-header__eyebrow">Student Portal</div>
                <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Schedule</h1>
                <p class="admin-page-subtitle mt-1 text-sm">
                    This schedule page is now present so the student route loads correctly.
                </p>
            </div>
        </section>

        <section class="student-reveal student-float rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
            style="--sd: 2;">
            <div class="text-lg font-black text-slate-900">Schedule view placeholder</div>
            <p class="mt-2 text-sm text-slate-600">
                You can keep using the subject page for timetable details, or we can build a full schedule page next.
            </p>
        </section>
    </div>
@endsection
