@extends('layout.admin.navbar')

@section('page')
    @php
        $contactTotal = max(0, (int) ($stats['total'] ?? 0));

        $panelClass =
            'contact-reveal contact-float rounded-[28px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80 dark:shadow-[0_24px_70px_-42px_rgba(0,0,0,0.9)]';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $filterTitleClass = 'text-xl font-bold text-slate-950 dark:text-white';

        $tableHeadClass =
            'admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';

        $tableBodyClass = 'divide-y divide-slate-100 bg-white dark:divide-slate-700 dark:bg-slate-900';
    @endphp

    <div class="contact-stage contact-page mx-auto max-w-[1500px] space-y-6 pb-8 text-slate-900 dark:text-slate-100">
        <x-admin.page-header reveal-class="contact-reveal" delay="1" icon="messages" title="Contact Inbox"
            subtitle="Review, filter, mark as read, and delete contact messages." />

        @if (session('success'))
            <div class="contact-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="contact-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        <section class="{{ $panelClass }}" style="--sd: 3;">
            <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-950 dark:text-white">Inbox</h2>
                        <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            Total messages: {{ number_format($contactTotal) }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if (($stats['unread'] ?? 0) > 0)
                            <form method="POST" action="{{ route('admin.contacts.readAll') }}" class="js-read-all-form">
                                @csrf

                                <button type="submit"
                                    class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 sm:px-4 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300 dark:hover:bg-indigo-500/25">
                                    Mark All Read
                                </button>
                            </form>
                        @endif

                        <button type="button" @click="filterOpen = true"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            <i class="fa-solid fa-filter text-xs"></i>
                            Filters
                        </button>
                    </div>
                </div>

                {{-- FILTER OVERLAY --}}
                <div x-show="filterOpen" x-cloak x-transition.opacity
                    class="fixed inset-0 z-[80] bg-slate-900/50 backdrop-blur-sm" @click="filterOpen = false"></div>

                {{-- FILTER DRAWER --}}
                <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">

                    <div class="flex h-full flex-col">
                        <div
                            class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                            <h3 class="text-3xl font-black text-slate-950 dark:text-white">Filters</h3>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.contacts.index') }}"
                                    class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                    Clear All
                                </a>

                                <button type="button" @click="filterOpen = false"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                    aria-label="Close filters">
                                    &times;
                                </button>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('admin.contacts.index') }}"
                            class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                            <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                <section class="space-y-2">
                                    <h4 class="{{ $filterTitleClass }}">Search</h4>
                                    <input id="q" name="q" type="text" value="{{ $search }}"
                                        placeholder="Search by name, email, subject, or message"
                                        class="{{ $inputClass }}">
                                </section>

                                <section class="space-y-2">
                                    <h4 class="{{ $filterTitleClass }}">Status</h4>
                                    <select name="status" class="{{ $inputClass }}">
                                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                                        <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Unread</option>
                                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Read</option>
                                    </select>
                                </section>
                            </div>

                            <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </aside>

                {{-- ACTIVE FILTERS --}}
                @if (($search ?? '') !== '' || ($status ?? 'all') !== 'all')
                    <div
                        class="flex flex-wrap items-center gap-2 rounded-2xl bg-indigo-50/70 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-indigo-500/10 dark:text-slate-300">
                        <span class="text-indigo-700 dark:text-indigo-300">Active filters:</span>

                        @if (($search ?? '') !== '')
                            <span
                                class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                Search: {{ $search }}
                            </span>
                        @endif

                        @if (($status ?? 'all') !== 'all')
                            <span
                                class="rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-indigo-300 dark:ring-indigo-400/20">
                                Status: {{ ucfirst($status) }}
                            </span>
                        @endif

                        <a href="{{ route('admin.contacts.index') }}"
                            class="ml-auto rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 hover:bg-indigo-100 dark:bg-slate-800 dark:text-indigo-300 dark:ring-indigo-400/20 dark:hover:bg-slate-700">
                            Clear
                        </a>
                    </div>
                @endif

                {{-- TABLE --}}
                <div class="min-w-0">
                    <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700">
                        <div class="contact-table-scroller max-h-[700px] overflow-auto">
                            <table class="admin-table contact-table w-full min-w-[1100px] text-left text-sm">
                                <thead class="{{ $tableHeadClass }}">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Sender</th>
                                        <th class="px-3 py-3 font-semibold">Subject</th>
                                        <th class="px-3 py-3 font-semibold">Message</th>
                                        <th class="px-3 py-3 font-semibold">Status</th>
                                        <th class="px-3 py-3 font-semibold">Received</th>
                                        <th class="px-3 py-3 text-right font-semibold">Actions</th>
                                    </tr>
                                </thead>

                                <tbody class="{{ $tableBodyClass }}">
                                    @forelse ($messages as $message)
                                        <tr class="align-top transition hover:bg-slate-50/80 dark:hover:bg-slate-800/70">
                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-800 dark:text-slate-100">
                                                    {{ $message->name }}
                                                </div>

                                                <a href="mailto:{{ $message->email }}"
                                                    class="text-xs font-semibold text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                    {{ $message->email }}
                                                </a>

                                                <div class="text-xs text-slate-400 dark:text-slate-500">
                                                    Phone: {{ $message->phone ?: '-' }}
                                                </div>
                                            </td>

                                            <td class="px-3 py-3">
                                                <div class="font-semibold text-slate-800 dark:text-slate-100">
                                                    {{ $message->subject ?: 'No subject' }}
                                                </div>
                                            </td>

                                            <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                                <div class="max-w-[360px] break-words">
                                                    {{ \Illuminate\Support\Str::limit($message->message, 120) }}
                                                </div>
                                            </td>

                                            <td class="px-3 py-3">
                                                @if ($message->is_read)
                                                    <span
                                                        class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                        Read
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                                                        <span class="status-dot h-2 w-2 rounded-full bg-amber-500"></span>
                                                        Unread
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-3 py-3 text-slate-500 dark:text-slate-400">
                                                <div>{{ $message->created_at->format('M d, Y h:i A') }}</div>
                                                <div class="text-xs text-slate-400 dark:text-slate-500">
                                                    {{ $message->created_at->diffForHumans() }}
                                                </div>
                                            </td>

                                            <td class="px-3 py-3">
                                                <div class="flex flex-wrap items-center justify-end gap-2">
                                                    @if (!$message->is_read)
                                                        <form method="POST"
                                                            action="{{ route('admin.contacts.read', $message) }}"
                                                            class="js-read-form" data-name="{{ $message->name }}">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit"
                                                                class="whitespace-nowrap rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-300 dark:hover:bg-indigo-500/25">
                                                                Mark Read
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span
                                                            class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                                            {{ $message->read_at?->diffForHumans() ?? 'Read' }}
                                                        </span>
                                                    @endif

                                                    <form method="POST"
                                                        action="{{ route('admin.contacts.destroy', $message) }}"
                                                        class="js-delete-form" data-name="{{ $message->name }}">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6"
                                                class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                                No contact messages found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="contact-pagination mt-5 text-slate-700 dark:text-slate-300">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/admin/contact.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
