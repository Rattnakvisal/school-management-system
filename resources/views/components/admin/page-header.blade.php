@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'dashboard',
    'revealClass' => '',
    'delay' => 1,
    'class' => '',
    'eyebrow' => '',
])

@php
    $sectionClass = trim($revealClass . ' admin-page-header admin-page-header--iconic overflow-hidden ' . $class);
    $iconName = strtolower((string) $icon);
@endphp

<section class="{{ $sectionClass }}" style="--sd: {{ $delay }};">
    <div class="admin-page-header__main flex flex-wrap items-start justify-between gap-5">
        <div class="admin-page-header__intro min-w-0 flex-1">
            <div class="admin-page-header__title-row flex items-center gap-3">
                <span class="admin-page-header__icon" aria-hidden="true">
                    @switch($iconName)
                        @case('students')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3Z" />
                                <path d="M8 11c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Z" />
                                <path d="M2 19v-1.5C2 15.57 5.13 14 8 14s6 1.57 6 3.5V19" />
                                <path d="M14 19v-1.5c0-1.23-.67-2.23-1.71-2.95" />
                                <path d="M22 19v-1.5C22 15.57 18.87 14 16 14h-1" />
                            </svg>
                        @break

                        @case('teachers')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5 2.5 10 12 15l9.5-5L12 5Z" />
                                <path d="M6 12.11V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-3.89" />
                                <path d="M21.5 10v4.5" />
                                <circle cx="21.5" cy="16.5" r="1.3" />
                            </svg>
                        @break

                        @case('staff')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3 5 5.8v5.4c0 4.4 2.9 8.5 7 9.8 4.1-1.3 7-5.4 7-9.8V5.8L12 3Z" />
                                <circle cx="12" cy="9.2" r="2.4" />
                                <path d="M7.8 16.5c.8-1.8 2.5-2.8 4.2-2.8s3.4 1 4.2 2.8" />
                            </svg>
                        @break

                        @case('classes')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3.5" y="4" width="17" height="14" rx="2.2" />
                                <path d="M8 20h8" />
                                <path d="M12 18v2" />
                                <path d="M7.5 9.5h9" />
                            </svg>
                        @break

                        @case('subjects')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 4.5h9.2a2.8 2.8 0 0 1 2.8 2.8V19.5H7.8A2.8 2.8 0 0 0 5 22V4.5Z" />
                                <path d="M19 19.5H9.8A2.8 2.8 0 0 0 7 22h9.2a2.8 2.8 0 0 0 2.8-2.5Z" />
                                <path d="M9 9h5.5" />
                                <path d="M9 12h5.5" />
                            </svg>
                        @break

                        @case('contacts')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2.3" />
                                <path d="m4.5 7 7.5 5 7.5-5" />
                                <circle cx="18.3" cy="8" r="1.6" fill="currentColor" stroke="none" />
                            </svg>
                        @break

                        @case('attendance')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="4" width="16" height="16" rx="2.3" />
                                <path d="m8 12 2.4 2.4L16.5 8.8" />
                                <path d="M8 4v2.5" />
                                <path d="M16 4v2.5" />
                            </svg>
                        @break

                        @case('time')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="8.5" />
                                <path d="M12 7.5v5l3.4 1.9" />
                                <path d="M12 2.5v2" />
                            </svg>
                        @break

                        @case('study')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7.5 12 3l9 4.5L12 12 3 7.5Z" />
                                <path d="M6 9.2V15c0 1.4 2.7 2.8 6 2.8s6-1.4 6-2.8V9.2" />
                                <path d="M21 13.2v3.6" />
                            </svg>
                        @break

                        @case('flag')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 21V4" />
                                <path d="M5 4h11l-1.5 4L16 12H5" />
                            </svg>
                        @break

                        @case('settings')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3.2" />
                                <path
                                    d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.6-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.6 1 1 0 0 0-.2-1.1l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2h.1a1 1 0 0 0 .6-.9V4a2 2 0 1 1 4 0v.2a1 1 0 0 0 .6.9h.1a1 1 0 0 0 1.1-.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1v.1a1 1 0 0 0 .9.6H20a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.9.6V15Z" />
                            </svg>
                        @break

                        @default
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3 3 8v8l9 5 9-5V8l-9-5Z" />
                                <path d="M12 12V3" />
                                <path d="m3 8 9 5 9-5" />
                            </svg>
                    @endswitch
                </span>
                <div class="min-w-0">
                    @if ($eyebrow !== '')
                        <div class="admin-page-header__eyebrow">{{ $eyebrow }}</div>
                    @endif
                    <h1 class="admin-page-title text-2xl font-black tracking-tight sm:text-3xl">{{ $title }}</h1>
                </div>
            </div>

            @if ($subtitle !== '')
                <p class="admin-page-subtitle mt-2 text-sm">{{ $subtitle }}</p>
            @endif

            @isset($actions)
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>

        @isset($stats)
            <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold sm:gap-3">
                {{ $stats }}
            </div>
        @endisset
    </div>
</section>
