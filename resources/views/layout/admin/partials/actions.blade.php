{{-- Actions partial: notifications, messages, profile --}}
<div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2 md:ml-0">
    {{-- Theme --}}
    <button type="button"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
        @click="toggleTheme()" :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
        :title="darkMode ? 'Light mode' : 'Dark mode'">
        <svg x-show="!darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M21.64 13.02A8.5 8.5 0 0 1 11 2.36 8.5 8.5 0 1 0 21.64 13.02Z" />
        </svg>
        <svg x-show="darkMode" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path
                d="M12 18a6 6 0 1 1 0-12 6 6 0 0 1 0 12Zm0 4-1-3h2l-1 3Zm0-20 1 3h-2l1-3Zm10 10-3 1v-2l3 1ZM2 12l3-1v2l-3-1Zm16.95 6.95-2.83-1.41 1.42-1.42 1.41 2.83ZM5.05 5.05l2.83 1.41-1.42 1.42L5.05 5.05Zm13.9 0-1.41 2.83-1.42-1.42 2.83-1.41ZM5.05 18.95l1.41-2.83 1.42 1.42-2.83 1.41Z" />
        </svg>
    </button>

    {{-- Notifications --}}
    <div class="relative" @click.outside="notifOpen=false">
        <button
            class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm
                       focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
            @click="profileOpen=false; messageOpen=false; notifOpen=!notifOpen" aria-label="Notifications"
            type="button">
            <svg class="h-5 w-5 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6V11a6 6 0 1 0-12 0v5L4 18v1h16v-1l-2-2Z" />
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

        <div x-show="notifOpen" x-cloak x-transition.origin.top.right
            class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                   sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-80">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                <div class="text-sm font-bold text-slate-900 dark:text-slate-100">Notifications</div>
                <span class="text-xs text-slate-500 dark:text-slate-400">Latest</span>
            </div>

            <div class="nav-scrollbar max-h-80 overflow-auto">
                @forelse(($navNotifs ?? []) as $n)
                    <a href="{{ $n->url ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                        <div class="flex items-start gap-3">
                            <span
                                class="mt-2 h-2 w-2 rounded-full {{ $n->is_read ? 'bg-slate-300' : 'bg-indigo-600' }}"></span>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $n->title }}</div>
                                <div class="text-xs text-slate-500 truncate dark:text-slate-400">{{ $n->message }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1 dark:text-slate-500">
                                    {{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No notifications yet.
                    </div>
                @endforelse
            </div>

            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                <form method="POST" action="{{ route($adminShellNotificationReadRoute) }}">
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
        <button
            class="relative p-2 rounded-xl bg-white border border-slate-200 hover:shadow-sm
                       focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
            @click="notifOpen=false; profileOpen=false; messageOpen=!messageOpen" aria-label="Messages" type="button">
            <svg class="h-5 w-5 text-slate-700 dark:text-slate-200" viewBox="0 0 24 24" fill="currentColor">
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

        <div x-show="messageOpen" x-cloak x-transition.origin.top.right
            class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                   sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-96">

            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                <div class="text-sm font-bold text-slate-900 dark:text-slate-100">Contact Messages</div>
                @if (($contactUnread ?? 0) > 0)
                    <span class="text-xs font-semibold text-amber-600">{{ $contactUnread }} unread</span>
                @endif
            </div>

            <div class="nav-scrollbar max-h-80 overflow-auto">
                @forelse(($navContacts ?? []) as $contact)
                    <a href="{{ route('admin.contacts.index') }}"
                        class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/80">
                        <div class="flex items-start gap-3">
                            <span
                                class="mt-2 h-2 w-2 rounded-full {{ $contact->is_read ? 'bg-slate-300' : 'bg-amber-500' }}"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $contact->name }}</div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500">
                                        {{ $contact->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="truncate text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $contact->subject }}</div>
                                <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ $contact->message }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No contact messages
                        yet.</div>
                @endforelse
            </div>

            <div class="grid gap-2 px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.contacts.index') }}"
                    class="w-full rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">Open
                    Contact Inbox</a>
                @if (($contactUnread ?? 0) > 0)
                    <form method="POST" action="{{ route('admin.contacts.readAll') }}">
                        @csrf
                        <button
                            class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Mark
                            all contact messages as read</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Profile --}}
    <div class="relative" @click.outside="profileOpen=false">
        <button
            class="ml-1 flex max-w-[12rem] items-center gap-2 rounded-full bg-white border border-slate-200 px-2.5 py-2 hover:shadow-sm sm:ml-2 sm:max-w-none sm:gap-3 sm:px-3
                   focus:outline-none focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800 dark:focus:ring-indigo-500/20"
            @click="notifOpen=false; messageOpen=false; profileOpen=!profileOpen" aria-label="Open profile menu"
            type="button">
            <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}"
                onerror="this.onerror=null;this.src='{{ auth()->user()->fallback_avatar_url }}';" alt="avatar">

            <div class="leading-tight hidden lg:block text-left">
                <div class="max-w-[10rem] truncate text-sm font-semibold text-slate-900 dark:text-slate-100">
                    {{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500 capitalize dark:text-slate-400">{{ auth()->user()->role }}</div>
            </div>

            <svg class="hidden h-4 w-4 text-slate-500 dark:text-slate-400 sm:block" viewBox="0 0 24 24"
                fill="currentColor">
                <path d="M7 10l5 5 5-5H7z" />
            </svg>
        </button>

        <div x-show="profileOpen" x-cloak x-transition.origin.top.right
            class="fixed left-3 right-3 top-20 z-[80] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40
                   sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-56">
            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                <div class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</div>
            </div>

            @if ($adminShellRole !== 'staff')
                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/80">
                    <span class="h-8 w-8 rounded-xl bg-slate-100 flex items-center justify-center dark:bg-slate-800">
                        <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
                        </svg>
                    </span>
                    Settings
                </a>
            @endif

            <div class="border-t border-slate-100 dark:border-slate-800"></div>

            <div class="p-2">
                <a href="{{ route('logout') }}"
                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 13v-2H7V8l-5 4 5 4v-3zM20 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z" />
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    </div>
