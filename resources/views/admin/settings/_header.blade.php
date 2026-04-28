<x-admin.page-header reveal-class="reveal" delay="1" icon="settings" title="Admin Settings"
    subtitle="Manage profile details, avatar, and account security.">
    <x-slot:actions>
        <a href="{{ route('admin.dashboard') }}"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
            Open Dashboard
        </a>
    </x-slot:actions>
</x-admin.page-header>
