@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="contact-stage space-y-6">
        <x-admin.page-header reveal-class="contact-reveal" delay="1" icon="contacts" title="Contact Messages"
            subtitle="Messages from the home page contact form.">
            <x-slot:stats>
                <span class="admin-page-stat">Total: {{ $stats['total'] }}</span>
                <span class="admin-page-stat admin-page-stat--amber">Unread:
                    {{ $stats['unread'] }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Read:
                    {{ $stats['read'] }}</span>
            </x-slot:stats>
        </x-admin.page-header>

        @if (session('success'))
            <div class="contact-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="contact-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        <section class="contact-reveal contact-float rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
            style="--sd: 3;">
            <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-black text-slate-900">Inbox</h2>
                    <div class="flex items-center gap-2">
                        @if ($stats['unread'] > 0)
                            <form method="POST" action="{{ route('admin.contacts.readAll') }}" class="js-read-all-form">
                                @csrf
                                <button type="submit"
                                    class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 sm:px-4">
                                    Mark All Read
                                </button>
                            </form>
                        @endif
                        <button type="button" @click="filterOpen = true"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                            </svg>
                            Filters
                        </button>
                    </div>
                </div>

                <div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                    @click="filterOpen = false"></div>

                <div class="grid gap-4">
                    <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                        <div class="flex h-full flex-col">
                            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('admin.contacts.index') }}"
                                        class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                        Clear All
                                    </a>
                                    <button type="button" @click="filterOpen = false"
                                        class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900"
                                        aria-label="Close filters">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.contacts.index') }}"
                                class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                        <input id="q" name="q" type="text" value="{{ $search }}"
                                            placeholder="Search by name, email, subject, or message"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    </section>
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                        <select name="status"
                                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                                            <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Unread
                                            </option>
                                            <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Read</option>
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
                            <div class="max-h-[700px] overflow-auto">
                                <table class="w-full min-w-[1100px] text-left text-sm">
                                    <thead
                                        class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-3 py-3 font-semibold">Sender</th>
                                            <th class="px-3 py-3 font-semibold">Subject</th>
                                            <th class="px-3 py-3 font-semibold">Message</th>
                                            <th class="px-3 py-3 font-semibold">Status</th>
                                            <th class="px-3 py-3 font-semibold">Received</th>
                                            <th class="px-3 py-3 font-semibold text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @forelse ($messages as $message)
                                            <tr class="align-top hover:bg-slate-50/80">
                                                <td class="px-3 py-3">
                                                    <div class="font-semibold text-slate-800">{{ $message->name }}</div>
                                                    <a href="mailto:{{ $message->email }}"
                                                        class="text-xs text-slate-500 pointer">{{ $message->email }}</a>
                                                    <div class="text-xs text-slate-400">Phone: {{ $message->phone ?: '-' }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3">
                                                    <div class="font-semibold text-slate-800">{{ $message->subject }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-3 text-slate-600">
                                                    {{ \Illuminate\Support\Str::limit($message->message, 120) }}
                                                </td>
                                                <td class="px-3 py-3">
                                                    @if ($message->is_read)
                                                        <span
                                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>Read
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                            <span
                                                                class="status-dot h-2 w-2 rounded-full bg-amber-500"></span>Unread
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3 text-slate-500">
                                                    {{ $message->created_at->format('M d, Y h:i A') }}
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
                                                                    class="whitespace-nowrap rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                                    Mark Read
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span
                                                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-500">
                                                                {{ $message->read_at?->diffForHumans() ?? 'Read' }}
                                                            </span>
                                                        @endif

                                                        <form method="POST"
                                                            action="{{ route('admin.contacts.destroy', $message) }}"
                                                            class="js-delete-form" data-name="{{ $message->name }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-500">
                                                    No contact messages found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-5">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/admin/contact.js'])
@endsection
