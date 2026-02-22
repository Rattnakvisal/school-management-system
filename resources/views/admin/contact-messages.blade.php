@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="contact-stage space-y-6">
        <section
            class="contact-reveal overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-900 to-blue-800 p-6 text-white shadow-lg"
            style="--sd: 1;">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Contact Messages</h1>
                    <p class="mt-1 text-sm text-indigo-100">Messages from the home page contact form.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="rounded-full bg-white/15 px-3 py-1.5">Total: {{ $stats['total'] }}</span>
                    <span class="rounded-full bg-amber-300/20 px-3 py-1.5 text-amber-100">Unread:
                        {{ $stats['unread'] }}</span>
                    <span class="rounded-full bg-emerald-400/20 px-3 py-1.5 text-emerald-100">Read:
                        {{ $stats['read'] }}</span>
                </div>
            </div>
        </section>

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

        <section class="contact-reveal contact-float rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200" style="--sd: 3;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-black text-slate-900">Inbox</h2>

                <div class="flex w-full max-w-3xl flex-wrap items-center justify-end gap-2">
                    <form method="GET" action="{{ route('admin.contacts.index') }}"
                        class="grid w-full gap-2 sm:grid-cols-[1fr_auto_auto]">
                        <input id="q" name="q" type="text" value="{{ $search }}" placeholder="Search by name, email, subject, or message"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <select name="status"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                            <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Unread</option>
                            <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Read</option>
                        </select>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white">Filter</button>
                    </form>

                    @if ($stats['unread'] > 0)
                        <form method="POST" action="{{ route('admin.contacts.readAll') }}" class="js-read-all-form">
                            @csrf
                            <button type="submit"
                                class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                Mark All Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-3 font-semibold">Sender</th>
                            <th class="px-3 py-3 font-semibold">Subject</th>
                            <th class="px-3 py-3 font-semibold">Message</th>
                            <th class="px-3 py-3 font-semibold">Status</th>
                            <th class="px-3 py-3 font-semibold">Received</th>
                            <th class="px-3 py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($messages as $message)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <div class="font-semibold text-slate-800">{{ $message->name }}</div>
                                    <a href="mailto:{{ $message->email }}" class="text-xs text-slate-500 pointer">{{ $message->email }}</a>
                                    <div class="text-xs text-slate-400">Phone: {{ $message->phone ?: '-' }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-semibold text-slate-800">{{ $message->subject }}</div>
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
                                            <span class="status-dot h-2 w-2 rounded-full bg-amber-500"></span>Unread
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-slate-500">
                                    {{ $message->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @if (!$message->is_read)
                                            <form method="POST" action="{{ route('admin.contacts.read', $message) }}"
                                                class="js-read-form" data-name="{{ $message->name }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                    Mark Read
                                                </button>
                                            </form>
                                        @else
                                            <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-500">
                                                {{ $message->read_at?->diffForHumans() ?? 'Read' }}
                                            </span>
                                        @endif

                                        <form method="POST" action="{{ route('admin.contacts.destroy', $message) }}"
                                            class="js-delete-form" data-name="{{ $message->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
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

            <div class="mt-5">
                {{ $messages->links() }}
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal === 'undefined') {
                return;
            }

            const confirmSubmit = (selector, buildConfig) => {
                document.querySelectorAll(selector).forEach((form) => {
                    form.addEventListener('submit', function(event) {
                        if (form.dataset.confirmed === '1') {
                            return;
                        }

                        event.preventDefault();
                        Swal.fire(buildConfig(form)).then((result) => {
                            if (result.isConfirmed) {
                                form.dataset.confirmed = '1';
                                form.submit();
                            }
                        });
                    });
                });
            };

            confirmSubmit('.js-read-form', (form) => ({
                title: 'Mark as read?',
                text: `Mark message from ${form.dataset.name || 'this sender'} as read.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, mark read',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-read-all-form', () => ({
                title: 'Mark all as read?',
                text: 'This will mark all unread contact messages as read.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-delete-form', (form) => ({
                title: 'Delete message?',
                text: `Delete contact message from ${form.dataset.name || 'this sender'}. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626'
            }));

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error'))
                });
            @elseif (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    timer: 2200,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endsection
