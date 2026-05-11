{{-- @php
    $isHomepageUiPage = $isHomepageUiPage ?? request()->routeIs('admin.homepage.index');
@endphp

<x-admin.page-header reveal-class="reveal" delay="1" icon="{{ $isHomepageUiPage ? 'palette' : 'settings' }}"
    title="{{ $isHomepageUiPage ? 'Homepage UI' : 'Admin Settings' }}"
    subtitle="{{ $isHomepageUiPage ? 'Manage the public homepage sections, content, images, and navigation.' : 'Manage profile details, avatar, and account security.' }}">
    <x-slot:actions>
        <a href="{{ $isHomepageUiPage ? route('home') : route('admin.dashboard') }}"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
            {{ $isHomepageUiPage ? 'View Homepage' : 'Open Dashboard' }}
        </a>
    </x-slot:actions>
</x-admin.page-header> --}}
