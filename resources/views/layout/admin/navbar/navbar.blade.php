<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? 'Admin Dashboard' }}</title>

    {{-- If using Vite Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class=" bg-slate-100">
    <div x-data="{
        mobile: false,
        notifOpen: false,
        messageOpen: false,
        profileOpen: false,
        closeAll() {
            this.notifOpen = false;
            this.messageOpen = false;
            this.profileOpen = false;
        }
    }" @keydown.escape.window="closeAll()" class="min-h-screen">

        {{-- MOBILE OVERLAY --}}
        <div x-show="mobile" x-transition class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="mobile=false"></div>

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 shadow-sm
           transition-all duration-300 lg:translate-x-0
           h-screen flex flex-col"
            :class="mobile ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            {{-- Header --}}
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-600/10 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold tracking-tight text-slate-900">Schooli</div>
                        <div class="text-xs text-slate-500 -mt-0.5">Admin Panel</div>
                    </div>
                </a>

                <button class="lg:hidden p-2 rounded-xl hover:bg-slate-100" @click="mobile=false" aria-label="Close">
                    ✕
                </button>
            </div>

            {{-- Nav (scroll area) --}}
            <nav class="px-3 py-4 space-y-1 flex-1 overflow-y-auto">
                @php
                    $item = fn($route, $label, $icon, $badge = 0) => [
                        'route' => $route,
                        'label' => $label,
                        'icon' => $icon,
                        'badge' => max((int) $badge, 0),
                        'active' => request()->routeIs($route),
                    ];
                    $links = [
                        $item('admin.dashboard', 'Dashboard', 'home'),
                        $item('admin.students.index', 'Students', 'users'),
                        $item('admin.teachers.index', 'Teachers', 'user'),
                        $item('admin.classes.index', 'Classes', 'grid'),
                        $item('admin.subjects.index', 'Subjects', 'book'),
                        $item('admin.attendance.index', 'Attendance', 'check'),
                        $item('admin.exams.index', 'Exams', 'clipboard'),
                        $item('admin.notices.index', 'Notices', 'bell'),
                        $item('admin.contacts.index', 'Contacts', 'mail', $contactUnread ?? 0),
                        $item('admin.settings', 'Settings', 'cog'),
                    ];
                @endphp

                @foreach ($links as $l)
                    <a href="{{ route($l['route']) }}"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                      {{ $l['active'] ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' }}">

                        <span
                            class="h-9 w-9 rounded-xl flex items-center justify-center
                             {{ $l['active'] ? 'bg-white/15' : 'bg-slate-100 group-hover:bg-white' }} relative">
                            @switch($l['icon'])
                                @case('home')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 3 2 12h3v9h6v-6h2v6h6v-9h3L12 3Z" />
                                    </svg>
                                @break

                                @case('users')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M16 11c1.66 0 3-1.34 3-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3Zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5Z" />
                                    </svg>
                                @break

                                @case('user')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
                                    </svg>
                                @break

                                @case('grid')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 3h8v8H3V3Zm10 0h8v8h-8V3ZM3 13h8v8H3v-8Zm10 0h8v8h-8v-8Z" />
                                    </svg>
                                @break

                                @case('book')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12v-2H6V4h12v16h2V4a2 2 0 0 0-2-2Z" />
                                    </svg>
                                @break

                                @case('check')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41L9 16.17Z" />
                                    </svg>
                                @break

                                @case('clipboard')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M16 2H8v2H5v18h14V4h-3V2Zm1 18H7V6h10v14Z" />
                                    </svg>
                                @break

                                @case('bell')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
                                    </svg>
                                @break

                                @case('mail')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 3-8 5L4 7V6l8 5 8-5v1Z" />
                                    </svg>
                                @break

                                @default
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                    </svg>
                            @endswitch

                            @if ($l['icon'] === 'mail' && ($l['badge'] ?? 0) > 0)
                                <span
                                    class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 {{ $l['active'] ? 'ring-indigo-600' : 'ring-white' }}"></span>
                            @endif
                        </span>

                        <span class="flex-1">{{ $l['label'] }}</span>

                        @if (($l['badge'] ?? 0) > 0)
                            <span
                                class="inline-grid min-w-[22px] place-items-center rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $l['active'] ? 'bg-white text-indigo-700' : 'bg-red-100 text-red-700' }}">
                                {{ $l['badge'] > 99 ? '99+' : $l['badge'] }}
                            </span>
                        @elseif ($l['active'])
                            <span class="h-2 w-2 rounded-full bg-white"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Footer (stays bottom) --}}
            <div class="p-4 border-t border-slate-100 shrink-0">
                <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-purple-600 p-4 text-white shadow-lg">
                    <div class="text-sm font-semibold">You’re on Admin Mode</div>
                    <div class="text-xs text-white/80 mt-1">Manage students, teachers and classes.</div>
                    <a href="#"
                        class="mt-3 inline-flex items-center justify-center rounded-2xl bg-white/15 px-3 py-2 text-xs font-semibold hover:bg-white/20">
                        Quick Guide
                    </a>
                </div>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="lg:pl-72">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-30 bg-slate-100/80 backdrop-blur border-b border-slate-200">
                <div class="h-16 px-4 sm:px-6 flex items-center gap-3">

                    {{-- Mobile menu button --}}
                    <button class="lg:hidden p-2 rounded-xl hover:bg-white" @click="mobile=true" aria-label="Open menu">
                        <svg class="h-6 w-6 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z" />
                        </svg>
                    </button>

                    {{-- Search --}}
                    <div class="flex-1">
                        <div class="relative max-w-xl">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M10 2a8 8 0 1 0 5.29 14.06l4.33 4.33 1.41-1.41-4.33-4.33A8 8 0 0 0 10 2Zm0 2a6 6 0 1 1 0 12 6 6 0 0 1 0-12Z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search anything here"
                                class="w-full rounded-full bg-white border border-slate-200 pl-11 pr-4 py-2.5
                                      text-sm outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-200" />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">

                        {{-- Notifications dropdown --}}
                        <div class="relative" @click.outside="notifOpen=false">
                            <button class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm"
                                @click="profileOpen=false; messageOpen=false; notifOpen=!notifOpen" aria-label="Notifications">

                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
                                </svg>

                                @if (($navUnread ?? 0) > 0)
                                    <span
                                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                        rounded-full bg-red-500 text-white text-[10px] font-bold
                        grid place-items-center ring-2 ring-white">
                                        {{ $navUnread }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="notifOpen" x-transition.origin.top.right
                                class="absolute right-0 mt-2 w-80 overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-xl">

                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">Notifications</div>
                                    <span class="text-xs text-slate-500">Latest</span>
                                </div>

                                <div class="max-h-80 overflow-auto">
                                    @forelse(($navNotifs ?? []) as $n)
                                        <a href="{{ $n->url ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50">

                                            <div class="flex items-start gap-3">
                                                {{-- dot unread --}}
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>

                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-slate-800">
                                                        {{ $n->title }}
                                                    </div>
                                                    <div class="text-xs text-slate-500 truncate">
                                                        {{ $n->message }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-400 mt-1">
                                                        {{ $n->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500">
                                            No notifications yet.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="px-4 py-3 border-t border-slate-100">
                                    <form method="POST" action="{{ route('admin.notifications.readAll') }}">
                                        @csrf
                                        <button
                                            class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                            Mark all as read
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Messages --}}
                        <div class="relative" @click.outside="messageOpen=false">
                            <button class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm"
                                @click="notifOpen=false; profileOpen=false; messageOpen=!messageOpen"
                                aria-label="Messages">
                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 4h16v12H6l-2 2V4Zm2 2v8h12V6H6Z" />
                                </svg>

                                @if (($contactUnread ?? 0) > 0)
                                    <span
                                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                        rounded-full bg-red-500 text-white text-[10px] font-bold
                                        grid place-items-center ring-2 ring-white">
                                        {{ $contactUnread > 99 ? '99+' : $contactUnread }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="messageOpen" x-transition.origin.top.right
                                class="absolute right-0 mt-2 w-96 overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-xl">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">Contact Messages</div>
                                    @if (($contactUnread ?? 0) > 0)
                                        <span class="text-xs font-semibold text-amber-600">{{ $contactUnread }} unread</span>
                                    @endif
                                </div>

                                <div class="max-h-80 overflow-auto">
                                    @forelse(($navContacts ?? []) as $contact)
                                        <a href="{{ route('admin.contacts.index') }}"
                                            class="block px-4 py-3 hover:bg-slate-50">
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="mt-2 h-2 w-2 rounded-full {{ $contact->is_read ? 'bg-slate-300' : 'bg-amber-500' }}"></span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="truncate text-sm font-semibold text-slate-800">
                                                            {{ $contact->name }}
                                                        </div>
                                                        <div class="text-[11px] text-slate-400">
                                                            {{ $contact->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    <div class="truncate text-xs font-semibold text-slate-600">
                                                        {{ $contact->subject }}
                                                    </div>
                                                    <div class="truncate text-xs text-slate-500">
                                                        {{ $contact->message }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500">
                                            No contact messages yet.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="grid gap-2 px-4 py-3 border-t border-slate-100">
                                    <a href="{{ route('admin.contacts.index') }}"
                                        class="w-full rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                        Open Contact Inbox
                                    </a>
                                    @if (($contactUnread ?? 0) > 0)
                                        <form method="POST" action="{{ route('admin.contacts.readAll') }}">
                                            @csrf
                                            <button
                                                class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                                Mark all contact messages as read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Profile dropdown --}}
                        <div class="relative" @click.outside="profileOpen=false">
                            <button
                                class="ml-2 flex items-center gap-3 rounded-full bg-white border border-slate-200 px-3 py-2 hover:shadow-sm"
                                @click="notifOpen=false; messageOpen=false; profileOpen=!profileOpen" aria-label="Open profile menu">
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ auth()->user()->avatar_url }}"
                                    onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';"
                                    alt="avatar">

                                <div class="leading-tight hidden sm:block text-left">
                                    <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</div>
                                </div>

                                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 10l5 5 5-5H7z" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-transition.origin.top.right
                                class="absolute right-0 mt-2 w-56 rounded-2xl bg-white border border-slate-200 shadow-xl overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                                </div>

                                <a href="#"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <span class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
                                        </svg>
                                    </span>
                                    Profile
                                </a>

                                <a href="{{ route('admin.settings') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <span class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                                        </svg>
                                    </span>
                                    Settings
                                </a>

                                <div class="border-t border-slate-100"></div>

                                <form method="POST" action="{{ route('logout') }}" class="p-2">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="p-4 sm:p-6">
                @yield('page')
            </main>
        </div>
    </div>

    {{-- Alpine --}}
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
