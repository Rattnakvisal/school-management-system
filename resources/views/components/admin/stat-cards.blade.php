@props([
    'cards' => [],
    'revealClass' => 'reveal',
    'floatClass' => 'float-card',
    'delayStart' => 2,
    'gridClass' => 'grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5',
])

<section class="{{ $gridClass }}">
    @foreach ($cards as $index => $card)
        @php
            $cardTotal = max(1, (int) ($card['total'] ?? 0));
            $visibleTotal = max(0, (int) ($card['total'] ?? 0));
            $cardActive = max(0, (int) ($card['active'] ?? 0));
            $progressActive = min($cardActive, $cardTotal);
            $progress = (int) round(($progressActive / $cardTotal) * 100);
            $tone = (string) ($card['tone'] ?? 'from-indigo-100 to-white text-indigo-600');
            $icon = (string) ($card['icon'] ?? 'records');
            $barTone = (string) ($card['barTone'] ?? 'from-indigo-500 to-cyan-400');
            $showPercent = (bool) ($card['showPercent'] ?? false);
            $progressText = (string) ($card['progressText'] ?? ($visibleTotal > 0 ? number_format($cardActive) . ' of ' . number_format($visibleTotal) . ' complete' : 'No records yet'));
            $displayActive = (string) ($card['displayActive'] ?? number_format($cardActive));
            $displayTotal = (string) ($card['displayTotal'] ?? number_format($visibleTotal));
            $hideTotal = (bool) ($card['hideTotal'] ?? false);
        @endphp

        <div class="{{ $revealClass }} {{ $floatClass }} min-h-[132px] rounded-[26px] border border-white/80 bg-white/90 p-5 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] backdrop-blur"
            style="--sd: {{ (int) $delayStart + $index }};">
            <div class="flex items-start justify-between gap-4">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br {{ $tone }}">
                    @switch($icon)
                        @case('students')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-8 0v2" />
                                <circle cx="12" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        @break

                        @case('teachers')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="7" r="4" />
                                <path d="M6 21v-2a6 6 0 0 1 12 0v2" />
                                <path d="M9 11h6" />
                            </svg>
                        @break

                        @case('classes')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="4" y="5" width="16" height="12" rx="2" />
                                <path d="M8 21h8" />
                                <path d="M12 17v4" />
                                <path d="M8 9h8" />
                                <path d="M8 13h5" />
                            </svg>
                        @break

                        @case('staff')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 3 4 7v6c0 5 3.5 7.5 8 8 4.5-.5 8-3 8-8V7l-8-4Z" />
                                <path d="M9 12l2 2 4-4" />
                            </svg>
                        @break

                        @case('active')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                        @break

                        @case('inactive')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        @break

                        @case('assigned')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m12 3 8 4-8 4-8-4 8-4Z" />
                                <path d="m4 12 8 4 8-4" />
                                <path d="m4 17 8 4 8-4" />
                            </svg>
                        @break

                        @case('subjects')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />
                                <path d="M8 7h8" />
                                <path d="M8 11h6" />
                            </svg>
                        @break

                        @case('time')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 2" />
                            </svg>
                        @break

                        @case('study')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 7 12 3l9 4-9 4-9-4Z" />
                                <path d="M6 9.5V15c0 1.7 2.7 3 6 3s6-1.3 6-3V9.5" />
                                <path d="M21 12v4" />
                            </svg>
                        @break

                        @case('mission')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 21V4" />
                                <path d="M5 4h11l-1.5 4L16 12H5" />
                            </svg>
                        @break

                        @case('assignment')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M8 3.5h8A2.5 2.5 0 0 1 18.5 6v14l-3-1.75L12.5 20l-3-1.75L6.5 20V6A2.5 2.5 0 0 1 9 3.5Z" />
                                <path d="M9.5 8h5" />
                                <path d="M9.5 11.5h5" />
                            </svg>
                        @break

                        @case('grades')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 4.5h6A1.5 1.5 0 0 1 16.5 6v1H19v13H5V7h2.5V6A1.5 1.5 0 0 1 9 4.5Z" />
                                <path d="M9 4.5h6v3H9v-3Z" />
                                <path d="M8.5 13h7" />
                                <path d="M8.5 16.5h4" />
                            </svg>
                        @break

                        @case('average')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 3v18" />
                                <path d="m17 8-5-5-5 5" />
                                <path d="M5 21h14" />
                            </svg>
                        @break

                        @case('pending')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 3" />
                            </svg>
                        @break

                        @case('attendance')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M8 6h13" />
                                <path d="M8 12h13" />
                                <path d="M8 18h13" />
                                <path d="m3 6 1 1 2-2" />
                                <path d="m3 12 1 1 2-2" />
                                <path d="m3 18 1 1 2-2" />
                            </svg>
                        @break

                        @case('contacts')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <path d="m3 7 9 6 9-6" />
                            </svg>
                        @break

                        @case('female')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="7" r="4" />
                                <path d="M12 11v9" />
                                <path d="M8.5 16h7" />
                            </svg>
                        @break

                        @case('male')
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="10" cy="14" r="4" />
                                <path d="m13 11 6-6" />
                                <path d="M15 5h4v4" />
                            </svg>
                        @break

                        @default
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                <path d="M14 2v6h6" />
                                <path d="M8 13h8" />
                                <path d="M8 17h5" />
                            </svg>
                    @endswitch
                </span>

                <span class="text-slate-300" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                    </svg>
                </span>
            </div>

            <div class="mt-5 flex items-end gap-1 text-2xl font-black text-slate-950">
                <span>{{ $displayActive }}</span>
                @unless ($hideTotal)
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ $displayTotal }}</span>
                @endunless
            </div>
            <div class="mt-1 text-sm font-bold text-slate-600">{{ $card['label'] ?? 'Total' }}</div>
            <div class="mt-1 text-[11px] font-semibold text-slate-400">
                @if ($showPercent)
                    {{ $progressText }}
                @else
                    {{ $card['activeLabel'] ?? 'Active' }}: {{ $displayActive }}
                @endif
            </div>
            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                <span class="block h-full rounded-full bg-gradient-to-r {{ $barTone }}"
                    style="width: {{ min(100, max(0, $progress)) }}%"></span>
            </div>
        </div>
    @endforeach
</section>
