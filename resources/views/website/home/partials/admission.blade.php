{{-- =========================
    Admissions Section
========================= --}}
<section id="admission" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @php
        $admissionStepIconTones = [
            'bg-cyan-300/15 text-cyan-200 ring-1 ring-cyan-200/20',
            'bg-amber-300/15 text-amber-200 ring-1 ring-amber-200/20',
            'bg-emerald-300/15 text-emerald-200 ring-1 ring-emerald-200/20',
            'bg-rose-300/15 text-rose-200 ring-1 ring-rose-200/20',
        ];

        $admissionStepIconToneStyle = function ($color) {
            $color = trim((string) $color);

            return preg_match('/^#[0-9A-Fa-f]{6}$/', $color)
                ? "color: {$color}; background-color: {$color}1F; border: 1px solid {$color}40;"
                : null;
        };
    @endphp

    <div data-reveal
        class="relative overflow-hidden rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-7 text-white shadow-xl sm:p-8 lg:p-10">
        {{-- Background Glow --}}
        <div class="absolute -left-20 top-10 h-72 w-72 rounded-full bg-indigo-400/20 blur-3xl"></div>
        <div class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-blue-500/20 blur-3xl"></div>

        <div class="relative grid gap-10 lg:grid-cols-12">

            {{-- Left Content --}}
            <div class="lg:col-span-5">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-blue-100">
                    <span class="h-2 w-2 rounded-full bg-blue-300"></span>
                    {{ $admissionBadge ?? __('home.admission.badge') }}
                </span>

                <h2 class="mt-5 text-3xl font-semibold sm:text-4xl">
                    {{ $admissionTitle ?? __('home.admission.title') }}
                </h2>

                <p class="mt-4 max-w-xl text-sm leading-8 text-slate-100 sm:text-base">
                    {{ $admissionDescription ?? __('home.admission.description') }}
                </p>

                {{-- Intake Card --}}
                <div
                    class="group relative mt-7 overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white/[0.14]">
                    <div
                        class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-400/20 blur-2xl opacity-0 transition group-hover:opacity-100">
                    </div>

                    <div
                        class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md transition group-hover:scale-110">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 7V3m8 4V3" />
                            <rect x="3" y="5" width="18" height="16" rx="2" />
                            <path d="M3 11h18" />
                        </svg>
                    </div>

                    <p class="relative mt-4 text-[11px] font-extrabold uppercase tracking-[0.24em] text-blue-200">
                        {{ $admissionIntakeLabel ?? __('home.admission.open_intake_label') }}
                    </p>

                    <p class="relative mt-2 text-2xl font-semibold">
                        {{ $admissionIntakeTitle ?? __('home.admission.open_intake_title') }}
                    </p>

                    <p class="relative mt-2 text-sm leading-7 text-slate-200">
                        {{ $admissionIntakeDescription ?? __('home.admission.open_intake_description') }}
                    </p>

                    <div
                        class="relative mt-4 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="mt-7">
                    @guest
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                            {{ __('home.actions.apply_now') }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route($dashboardRoute) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                            {{ __('home.actions.continue_dashboard') }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endguest
                </div>
            </div>

            {{-- Right Steps --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7">
                @foreach ($steps as $i => $s)
                    @php
                        $iconStyle = $admissionStepIconToneStyle($s['color'] ?? null);
                        $iconTone = $iconStyle
                            ? ''
                            : (($s['color'] ?? null) ?: $admissionStepIconTones[$i % count($admissionStepIconTones)]);
                    @endphp

                    <article data-reveal style="--d:{{ number_format(0.06 * $loop->iteration, 2) }}s"
                        class="group relative overflow-hidden rounded-[1.6rem] border border-white/10 bg-white/10 p-5 backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white/[0.14]">
                        <div
                            class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-400/20 blur-2xl opacity-0 transition group-hover:opacity-100">
                        </div>

                        {{-- Icon same style as Academic Programs --}}
                        <div
                            class="relative flex h-12 w-12 items-center justify-center rounded-2xl {{ $iconTone }} shadow-md transition duration-300 group-hover:scale-110"
                            @if ($iconStyle) style="{{ $iconStyle }}" @endif>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="m5 12 5 5L20 7" />
                            </svg>
                        </div>

                        <p class="relative mt-4 text-[11px] font-extrabold uppercase tracking-[0.24em] text-blue-200">
                            {{ __('home.admission.step_label', ['number' => str_pad($i + 1, 2, '0', STR_PAD_LEFT)]) }}
                        </p>

                        <p class="relative mt-2 text-base font-semibold text-white">
                            {{ $s['title'] }}
                        </p>

                        <p class="relative mt-2 text-sm leading-7 text-slate-200">
                            {{ $s['description'] }}
                        </p>

                        <div
                            class="relative mt-4 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                        </div>
                    </article>
                @endforeach
            </div>

        </div>
    </div>
</section>
