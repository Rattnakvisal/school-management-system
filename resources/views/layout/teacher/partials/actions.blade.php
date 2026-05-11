{{-- Actions partial: notifications and profile --}}
<div class="relative" @click.outside="notifOpen = false">
    <button
        class="relative rounded-xl border border-slate-200 bg-white p-2 hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100"
        @click="profileOpen = false; notifOpen = !notifOpen" aria-label="Notifications" type="button">
        <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
        </svg>

        <span id="teacher-notif-badge"
            class="absolute -top-1 -right-1 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white {{ ($navUnread ?? 0) > 0 ? '' : 'hidden' }}">
            {{ $navUnread ?? 0 }}
        </span>
    </button>

    <div x-show="notifOpen" x-cloak x-transition.origin.top.right
        class="fixed left-3 right-3 top-20 z-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-80">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <div class="text-sm font-bold text-slate-900">Notifications</div>
            <span class="text-xs text-slate-500">Latest</span>
        </div>

        <div id="teacher-notif-list" class="nav-scrollbar max-h-80 overflow-auto">
            @forelse (($navNotifs ?? []) as $n)
                <a href="{{ trim((string) ($n->url ?? '')) !== '' ? $n->url : route('teacher.notices.index') }}"
                    class="block px-4 py-3 hover:bg-slate-50">
                    <div class="flex items-start gap-3">
                        <span
                            class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-slate-800">
                                {{ $n->title }}
                            </div>
                            <div class="truncate text-xs text-slate-500">{{ $n->message }}
                            </div>
                            <div class="mt-1 text-[11px] text-slate-400">
                                {{ $n->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </a>
            @empty
                <div id="teacher-notif-empty" class="px-4 py-8 text-center text-sm text-slate-500">No notifications yet.
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-100 px-4 py-3">
            <div class="grid gap-2 sm:grid-cols-2">
                <a href="{{ route('teacher.notices.index') }}"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
                    View all
                </a>
                <form method="POST" action="{{ route('teacher.notifications.readAll') }}">
                    @csrf
                    <button
                        class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Mark all read
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="relative" @click.outside="profileOpen = false">
    <button
        class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2 hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100"
        @click="notifOpen = false; profileOpen = !profileOpen" aria-label="Open profile menu" type="button">
        <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}"
            onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';" alt="avatar">

        <div class="hidden text-left leading-tight sm:block">
            <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</div>
        </div>

        <svg class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="profileOpen ? 'rotate-180' : ''"
            fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 9l6 6 6-6" />
        </svg>
    </button>

    <div x-show="profileOpen" x-cloak x-transition.origin.top.right
        class="fixed left-3 right-3 top-20 z-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-56">
        <div class="border-b border-slate-100 px-4 py-3">
            <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
            <div class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</div>
        </div>

        <a href="{{ route('teacher.settings') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-50">
                <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                </svg>
            </span>
            Settings
        </a>

        <a href="{{ route('teacher.attendance.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-50">
                <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </span>
            Check Attendance
        </a>

        <div class="border-t border-slate-100"></div>

        <div class="p-2">
            <a href="{{ route('logout') }}"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
                </svg>
                Logout
            </a>
        </div>
    </div>
</div>
