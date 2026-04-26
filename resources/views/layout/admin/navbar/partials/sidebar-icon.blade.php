@php
    $iconName = strtolower((string) ($icon ?? 'cog'));
@endphp

@switch($iconName)
    @case('home')
    @case('layout-dashboard')
    @case('dashboard')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M4 4h7v7H4V4Zm9 0h7v5h-7V4Zm0 7h7v9h-7v-9Zm-9 2h7v7H4v-7Z" />
        </svg>
    @break

    @case('users')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M16 11c1.66 0 3-1.34 3-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3Zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5Z" />
        </svg>
    @break

    @case('user')
    @case('user-cog')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
            <path
                d="M18.5 13.5a.7.7 0 0 1 .69.58l.13.79.67.28.73-.34a.7.7 0 0 1 .84.15l.79.95a.7.7 0 0 1-.03.86l-.53.6.07.72.65.46a.7.7 0 0 1 .25.83l-.48 1.14a.7.7 0 0 1-.75.41l-.79-.11-.55.47-.08.8a.7.7 0 0 1-.59.62l-1.22.17a.7.7 0 0 1-.72-.38l-.36-.71-.69-.17-.61.52a.7.7 0 0 1-.87.01l-.95-.79a.7.7 0 0 1-.16-.84l.34-.73-.28-.67-.79-.13a.7.7 0 0 1-.58-.69v-1.21a.7.7 0 0 1 .58-.69l.79-.13.28-.67-.34-.73a.7.7 0 0 1 .15-.84l.95-.79a.7.7 0 0 1 .86.03l.6.53.72-.07.46-.65a.7.7 0 0 1 .83-.25l1.14.48a.7.7 0 0 1 .41.75l-.11.79.47.55.8.08a.7.7 0 0 1 .62.59l.17 1.22a.7.7 0 0 1-.38.72l-.71.36-.17.69.52.61a.7.7 0 0 1 .01.87l-.79.95a.7.7 0 0 1-.84.16l-.73-.34-.67.28-.13.79a.7.7 0 0 1-.69.58h-1.21Zm.61 2.1a1.4 1.4 0 1 0 0 2.8 1.4 1.4 0 0 0 0-2.8Z" />
        </svg>
    @break

    @case('shield')
    @case('shield-user')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2 4 5.2V11c0 5.05 3.41 9.76 8 11 4.59-1.24 8-5.95 8-11V5.2L12 2Zm0 4a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm5.1 10.1A8.72 8.72 0 0 1 12 19.84a8.72 8.72 0 0 1-5.1-3.74C7.63 14.84 9.9 14 12 14s4.37.84 5.1 2.1Z" />
        </svg>
    @break

    @case('grid')
    @case('layers')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M12 3 2 8l10 5 10-5-10-5Zm0 8.2L2.8 6.6 12 2l9.2 4.6L12 11.2Zm-8.9 2.1L12 18l8.9-4.7L22 15l-10 5-10-5 1.1-1.7Zm0 4L12 22l8.9-4.7L22 19l-10 5L2 19l1.1-1.7Z" />
        </svg>
    @break

    @case('book')
    @case('book-open')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M3 5.5A2.5 2.5 0 0 1 5.5 3H11v15H5.5A2.5 2.5 0 0 0 3 20.5v-15Zm18 0A2.5 2.5 0 0 0 18.5 3H13v15h5.5a2.5 2.5 0 0 1 2.5 2.5v-15Z" />
        </svg>
    @break

    @case('clock')
    @case('clock-3')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 11h5v-2h-4V6h-2v7Z" />
        </svg>
    @break

    @case('check')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="m9.55 17.6-5.3-5.3 1.42-1.42 3.88 3.89 8.78-8.78 1.42 1.42-10.2 10.19Z" />
        </svg>
    @break

    @case('academic')
    @case('graduation-cap')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3Zm0 10.83L4.74 10 12 6.17 19.26 10 12 13.83ZM4 13.2V17c0 1.66 3.58 3 8 3s8-1.34 8-3v-3.8l-8 4.37-8-4.37Z" />
        </svg>
    @break

    @case('user-check')
    @case('clipboard-check')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M16 2H8v2H5v18h14V4h-3V2Zm1 18H7V6h10v14Z" />
            <path d="m9.8 13.6 1.6 1.6 3.8-3.8-1.1-1.1-2.7 2.7-.5-.5-1.1 1.1Z" />
        </svg>
    @break

    @case('calendar-check')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V2Zm12 8H5v8h14v-8Z" />
            <path d="m9.8 14.6 1.6 1.6 2.8-2.8-1.1-1.1-1.7 1.7-.5-.5-1.1 1.1Z" />
        </svg>
    @break

    @case('clipboard')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M16 2H8v2H5v18h14V4h-3V2Zm1 18H7V6h10v14Z" />
        </svg>
    @break

    @case('document')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm-1 2.5L17.5 9H13V4.5ZM8 13h8v2H8v-2Zm0 4h8v2H8v-2Z" />
        </svg>
    @break

    @case('bell')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6v-5a6 6 0 1 0-12 0v5l-2 2v1h16v-1l-2-2Z" />
        </svg>
    @break

    @case('mail')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 3-8 5L4 7V6l8 5 8-5v1Z" />
        </svg>
    @break

    @case('flag')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 3a1 1 0 0 1 1 1v1h9.2l-.54.9a2 2 0 0 0 0 2.1l.68 1.13a2 2 0 0 1 0 2.07l-.68 1.13a2 2 0 0 0 0 2.1l.54.9H7v4a1 1 0 1 1-2 0V4a1 1 0 0 1 1-1Z" />
        </svg>
    @break

    @case('settings')
    @case('cog')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
        </svg>
    @break

    @default
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M19.14 12.94a7.6 7.6 0 0 0 .05-.94 7.6 7.6 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.28 7.28 0 0 0-1.63-.94l-.36-2.54A.5.5 0 0 0 13.9 1h-3.8a.5.5 0 0 0-.49.42l-.36 2.54c-.58.23-1.12.54-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 7.48a.5.5 0 0 0 .12.64l2.03 1.58c-.03.31-.05.63-.05.94s.02.63.05.94L2.83 14.5a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.64.22l2.39-.96c.5.4 1.05.71 1.63.94l.36 2.54c.04.24.25.42.49.42h3.8c.24 0 .45-.18.49-.42l.36-2.54c.58-.23 1.12-.54 1.63-.94l2.39.96c.24.1.51.01.64-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.56ZM12 15.5A3.5 3.5 0 1 1 12 8a3.5 3.5 0 0 1 0 7.5Z" />
        </svg>
@endswitch
