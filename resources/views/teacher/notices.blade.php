@extends('layout.teacher.navbar')

@section('page')
    @php
        $normalizeNotification = function ($notification): array {
            if (is_array($notification)) {
                return [
                    'title' => trim((string) ($notification['title'] ?? 'Notification')),
                    'message' => trim((string) ($notification['message'] ?? '')),
                    'url' => trim((string) ($notification['url'] ?? '')),
                    'is_read' => (bool) ($notification['is_read'] ?? false),
                    'created_at_human' => (string) ($notification['created_at_human'] ?? ''),
                ];
            }

            return [
                'title' => trim((string) ($notification->title ?? 'Notification')),
                'message' => trim((string) ($notification->message ?? '')),
                'url' => trim((string) ($notification->url ?? '')),
                'is_read' => (bool) ($notification->is_read ?? false),
                'created_at_human' =>
                    (string) ($notification->created_at_human ?? ($notification->created_at?->diffForHumans() ?? '')),
            ];
        };

        $notifications = collect($notifications ?? ($navNotifs ?? []))->map($normalizeNotification);
        $unreadCount = (int) ($unreadCount ?? ($navUnread ?? 0));
        $totalCount = $notifications->count();
    @endphp

    <div class="teacher-stage space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Recent notifications</h2>
                    <p class="mt-1 text-sm text-slate-500">Review the latest teacher alerts, approvals, and attendance
                        updates.</p>
                </div>
            </div>

            @if ($notifications->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($notifications as $notification)
                        <a href="{{ ($notification['url'] ?? '') !== '' ? $notification['url'] : route('teacher.dashboard') }}"
                            class="block rounded-2xl border {{ $notification['is_read'] ? 'border-slate-200 bg-white' : 'border-indigo-100 bg-indigo-50/60' }} px-4 py-4 transition hover:-translate-y-0.5 hover:shadow-sm">
                            <div class="flex items-start gap-4">
                                <span
                                    class="mt-1 h-3 w-3 rounded-full {{ $notification['is_read'] ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="truncate text-sm font-semibold text-slate-900">
                                            {{ $notification['title'] }}
                                        </div>
                                        <div class="text-[11px] font-medium text-slate-400">
                                            {{ $notification['created_at_human'] ?? '' }}
                                        </div>
                                    </div>
                                    <div class="mt-1 text-sm text-slate-600">
                                        {{ $notification['message'] !== '' ? $notification['message'] : 'No additional details provided.' }}
                                    </div>
                                    <div class="mt-3 text-xs font-semibold text-indigo-600">
                                        Open notification
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-4 py-12 text-center">
                    <div class="text-lg font-bold text-slate-900">No notifications yet</div>
                    <p class="mt-2 text-sm text-slate-500">
                        Teacher updates, law request alerts, and attendance messages will appear here.
                    </p>
                    <a href="{{ route('teacher.dashboard') }}"
                        class="mt-4 inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Go back to dashboard
                    </a>
                </div>
            @endif
        </section>
    </div>
@endsection
