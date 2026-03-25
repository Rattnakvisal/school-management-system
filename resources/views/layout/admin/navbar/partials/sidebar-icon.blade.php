@switch($icon ?? 'cog')
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

    @case('clock')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 11h5v-2h-4V6h-2v7Z" />
        </svg>
    @break

    @case('academic')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3Zm0 10.83L4.74 10 12 6.17 19.26 10 12 13.83ZM4 13.2V17c0 1.66 3.58 3 8 3s8-1.34 8-3v-3.8l-8 4.37-8-4.37Z" />
        </svg>
    @break

    @case('user-check')
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.31 0-8 1.66-8 5v1h10.4a6 6 0 0 1-.36-2c0-1.55.58-2.97 1.53-4H12Z" />
            <path d="m16.5 17 1.6 1.6L21 15.7l-1.1-1.1-1.8 1.8-.5-.5L16.5 17Z" />
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
