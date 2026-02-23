@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="study-stage space-y-6">
        <section
            class="study-reveal admin-page-header overflow-hidden"
            style="--sd: 1;">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Student Study</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">Students with selected class time, major subject, teacher, and created dates.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="admin-page-stat">Students: {{ $stats['students'] }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Subjects:
                        {{ $stats['subjects'] }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Teachers:
                        {{ $stats['teachers'] }}</span>
                    @if ($hasMajorSubjectColumn ?? false)
                        <span class="admin-page-stat admin-page-stat--indigo">With Major:
                            {{ $stats['withMajorSubject'] ?? 0 }}</span>
                    @endif
                    @if ($hasClassStudyTimeColumn ?? false)
                        <span class="admin-page-stat admin-page-stat--cyan">With Study Time:
                            {{ $stats['withStudyTime'] ?? 0 }}</span>
                    @endif
                </div>
            </div>
        </section>

        <section class="study-reveal study-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
            style="--sd: 2;">
            <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-black text-slate-900">Study List</h2>
                    <button type="button" @click="filterOpen = true"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                        </svg>
                        Filters
                    </button>
                </div>

                <div x-show="filterOpen" x-cloak x-transition.opacity
                    class="fixed inset-0 z-[80] bg-slate-900/40" @click="filterOpen = false"></div>

                <div class="grid gap-4">
                    <aside x-show="filterOpen" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                        <div class="flex h-full flex-col">
                            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('admin.student-study.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                        Clear All
                                    </a>
                                    <button type="button" @click="filterOpen = false"
                                        class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900" aria-label="Close filters">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.student-study.index') }}" class="flex min-h-0 flex-1 flex-col"
                                @submit="filterOpen = false">
                                <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                        <input id="q" name="q" type="text" value="{{ $search }}"
                                            placeholder="Search student, email, class, room, subject, time, code, or teacher"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    </section>
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                        <select name="class_id"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All Classes</option>
                                            @foreach ($classes as $classOption)
                                                <option value="{{ $classOption->id }}" {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                    {{ $classOption->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Subject</h4>
                                        <select name="subject_id"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ $subjectId === 'all' ? 'selected' : '' }}>All Subjects</option>
                                            @foreach ($subjects as $subjectOption)
                                                <option value="{{ $subjectOption->id }}" {{ $subjectId === (string) $subjectOption->id ? 'selected' : '' }}>
                                                    {{ $subjectOption->name }} ({{ $subjectOption->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Period</h4>
                                        <select name="period"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Periods</option>
                                            @foreach ($periodOptions as $periodKey => $periodLabel)
                                                <option value="{{ $periodKey }}" {{ $period === $periodKey ? 'selected' : '' }}>
                                                    {{ $periodLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>
                                </div>
                                <div class="border-t border-slate-200 px-5 py-4">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-3 text-lg font-bold text-white transition hover:bg-slate-800">
                                        Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </aside>

                    <div class="min-w-0">
                        <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                <div class="max-h-[560px] overflow-auto">
                    <table class="w-full min-w-[1220px] text-left text-sm">
                        <thead
                            class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-3 font-semibold">Student</th>
                                <th class="px-3 py-3 font-semibold">Class</th>
                                <th class="px-3 py-3 font-semibold">Selected Time</th>
                                <th class="px-3 py-3 font-semibold">Major Subject</th>
                                <th class="px-3 py-3 font-semibold">Subject Time</th>
                                <th class="px-3 py-3 font-semibold">Teacher</th>
                                <th class="px-3 py-3 font-semibold">Student Created</th>
                                <th class="px-3 py-3 font-semibold">Teacher Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($studies as $row)
                                @php
                                    $classLabel = trim((string) ($row->class_name ?? ''));
                                    $section = trim((string) ($row->class_section ?? ''));
                                    if ($classLabel !== '' && $section !== '') {
                                        $classLabel .= ' - ' . $section;
                                    } elseif ($classLabel === '') {
                                        $classLabel = 'Unassigned';
                                    }
                                    $classRoom = trim((string) ($row->class_room ?? ''));
                                    $studentCreatedAt = $row->student_created_at ? \Carbon\Carbon::parse($row->student_created_at) : null;
                                    $teacherCreatedAt = $row->teacher_created_at ? \Carbon\Carbon::parse($row->teacher_created_at) : null;
                                    $periodKey = strtolower(trim((string) ($row->class_study_period ?? '')));
                                    $periodLabel = $periodKey !== '' ? ($periodOptions[$periodKey] ?? ucfirst($periodKey)) : null;
                                @endphp
                                <tr class="align-top hover:bg-slate-50/80">
                                    <td class="px-3 py-3">
                                        <div class="font-semibold text-slate-800">{{ $row->student_name }}</div>
                                        <div class="text-xs text-slate-500">{{ $row->student_email ?: '-' }}</div>
                                        <div class="text-xs text-slate-400">ID #{{ str_pad((string) $row->student_id, 7, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">
                                        <div>{{ $classLabel }}</div>
                                        <div class="text-xs text-slate-400">{{ $classRoom !== '' ? 'Room: ' . $classRoom : 'Room: -' }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">
                                        @if ($periodLabel)
                                            <span
                                                class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">{{ $periodLabel }}</span>
                                        @endif
                                        @if ($row->class_study_start_time && $row->class_study_end_time)
                                            <div class="mt-1 font-medium">
                                                {{ \Carbon\Carbon::parse($row->class_study_start_time)->format('h:i A') }} ->
                                                {{ \Carbon\Carbon::parse($row->class_study_end_time)->format('h:i A') }}
                                            </div>
                                        @elseif ($row->class_study_start_time)
                                            <div class="mt-1 font-medium">{{ \Carbon\Carbon::parse($row->class_study_start_time)->format('h:i A') }}</div>
                                        @else
                                            <div class="mt-1">-</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">
                                        @if ($row->subject_name)
                                            <div>{{ $row->subject_name }}</div>
                                            <div class="text-xs text-slate-400">{{ $row->subject_code ? '(' . $row->subject_code . ')' : '-' }}</div>
                                        @else
                                            Unassigned
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">
                                        @if ($row->subject_study_start_time && $row->subject_study_end_time)
                                            {{ \Carbon\Carbon::parse($row->subject_study_start_time)->format('h:i A') }} ->
                                            {{ \Carbon\Carbon::parse($row->subject_study_end_time)->format('h:i A') }}
                                        @elseif ($row->subject_study_start_time)
                                            {{ \Carbon\Carbon::parse($row->subject_study_start_time)->format('h:i A') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">
                                        <div>{{ $row->teacher_name ?: 'Unassigned' }}</div>
                                        <div class="text-xs text-slate-400">{{ $row->teacher_email ?: '-' }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-500">
                                        @if ($studentCreatedAt)
                                            <div>{{ $studentCreatedAt->format('M d, Y h:i A') }}</div>
                                            <div class="text-xs text-slate-400">{{ $studentCreatedAt->diffForHumans() }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-500">
                                        @if ($teacherCreatedAt)
                                            <div>{{ $teacherCreatedAt->format('M d, Y h:i A') }}</div>
                                            <div class="text-xs text-slate-400">{{ $teacherCreatedAt->diffForHumans() }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-500">
                                        No student study records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">
                {{ $studies->links() }}
            </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
