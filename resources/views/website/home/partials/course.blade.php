{{-- =========================
    Courses Section
========================= --}}
<section id="course" class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
    @php
        $courseItems = array_values(array_filter($courseCards ?? $steps ?? [], fn($course) => filled($course['title'] ?? null)));
        $courseCategories = collect($courseItems)
            ->pluck('category')
            ->filter()
            ->unique()
            ->take(5)
            ->values()
            ->all();

        $courseToneStyle = function ($color, string $fallback = '#10b981') {
            $color = trim((string) $color);
            $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : $fallback;

            return "color: {$color}; background-color: {$color}14; border-color: {$color}24;";
        };

        $courseBorderStyle = function ($color, string $fallback = '#10b981') {
            $color = trim((string) $color);
            $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : $fallback;

            return "border-color: {$color}24; box-shadow: 0 18px 42px -34px {$color};";
        };
    @endphp

    <div class="relative overflow-hidden bg-[#effff9] px-5 py-10 sm:px-8 lg:px-12">
        <div class="pointer-events-none absolute right-8 top-8 hidden grid-cols-6 gap-1.5 text-emerald-300 sm:grid">
            @for ($i = 0; $i < 30; $i++)
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            @endfor
        </div>

        <div class="pointer-events-none absolute -left-16 top-12 h-44 w-44 rounded-full border border-emerald-300/50"></div>
        <div class="pointer-events-none absolute -left-4 top-24 h-64 w-64 rounded-full border border-emerald-200/70"></div>

        <div data-reveal class="relative mx-auto max-w-3xl text-center">
            <span class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                {{ $courseBadge ?? \App\Support\HomePageContent::text('course.badge') }}
            </span>

            <h2
                class="[font-family:Outfit,_sans-serif] mt-3 text-3xl font-extrabold leading-tight tracking-tight text-[#10221e] sm:text-4xl">
                {{ $courseTitle ?? \App\Support\HomePageContent::text('course.title') }}
            </h2>

            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                {{ $courseDescription ?? \App\Support\HomePageContent::text('course.description') }}
            </p>
        </div>

        @if (!empty($courseItems))
            <div data-reveal style="--d:.06s" class="relative mt-8 flex flex-wrap items-center justify-center gap-3">
                <button type="button"
                    class="inline-flex min-w-28 items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" />
                    </svg>
                    All
                </button>

                @foreach ($courseCategories as $category)
                    <button type="button"
                        class="inline-flex min-w-28 items-center justify-center gap-2 rounded-full border border-emerald-900/5 bg-white/75 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-slate-600 shadow-[0_16px_36px_-32px_rgba(15,118,110,0.55)] backdrop-blur transition hover:-translate-y-0.5 hover:bg-white hover:text-emerald-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M12 3 2 8l10 5 10-5-10-5Z" />
                            <path d="m2 13 10 5 10-5" />
                        </svg>
                        {{ $category }}
                    </button>
                @endforeach
            </div>

            <div class="relative mt-9 grid gap-4 lg:grid-cols-3">
                @foreach ($courseItems as $course)
                    @php
                        $courseColor = $course['color'] ?? ['#3b82f6', '#f97316', '#10b981'][$loop->index % 3];
                        $initials = collect(explode(' ', trim((string) ($course['instructor_name'] ?? ''))))
                            ->filter()
                            ->map(fn($part) => mb_substr($part, 0, 1))
                            ->take(2)
                            ->implode('');
                    @endphp

                    <article data-reveal style="--d:{{ number_format(0.08 * $loop->iteration, 2) }}s; {{ $courseBorderStyle($courseColor) }}"
                        class="group overflow-hidden border bg-white/85 p-3 shadow-[0_16px_36px_-32px_rgba(15,118,110,0.55)] backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white">
                        <div class="relative overflow-hidden bg-slate-100">
                            <img src="{{ $course['image_url'] ?? asset('images/study.jpg') }}"
                                alt="{{ $course['title'] ?? 'Course' }}" loading="lazy"
                                class="h-52 w-full object-cover transition duration-500 group-hover:scale-105">

                            <span
                                class="absolute right-4 top-4 grid h-11 w-11 place-items-center rounded-full border bg-white/90 text-emerald-600 shadow-[0_16px_36px_-28px_rgba(15,118,110,0.65)]"
                                style="{{ $courseToneStyle($courseColor) }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M19 21 12 17 5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16Z" />
                                </svg>
                            </span>
                        </div>

                        <div class="px-3 pb-3 pt-5">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-extrabold uppercase"
                                style="{{ $courseToneStyle($courseColor) }}">
                                {{ $course['category'] ?? 'Course' }}
                            </span>

                            <h3 class="mt-4 text-xl font-extrabold leading-snug tracking-tight text-[#10221e]">
                                {{ $course['title'] ?? '' }}
                            </h3>

                            <p class="mt-3 min-h-14 text-sm leading-7 text-slate-600">
                                {{ $course['description'] ?? '' }}
                            </p>

                            <div class="mt-5 flex items-center justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full text-sm font-black text-white"
                                        style="background-color: {{ preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $courseColor) ? $courseColor : '#10b981' }}">
                                        {{ $initials ?: 'TB' }}
                                    </span>

                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-extrabold text-slate-900">
                                            {{ $course['instructor_name'] ?? '' }}
                                        </span>
                                        <span class="block truncate text-xs text-slate-500">
                                            {{ $course['instructor_role'] ?? '' }}
                                        </span>
                                    </span>
                                </div>

                                @if (filled($course['rating'] ?? null))
                                    <div class="shrink-0 text-right">
                                        <span class="inline-flex items-center gap-1 font-bold text-slate-900">
                                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="m12 2.5 2.9 5.87 6.48.94-4.69 4.57 1.11 6.45L12 17.28l-5.8 3.05 1.11-6.45-4.69-4.57 6.48-.94L12 2.5Z" />
                                            </svg>
                                            {{ $course['rating'] }}
                                        </span>
                                        @if (filled($course['review_count'] ?? null))
                                            <span class="block text-xs text-slate-500">({{ $course['review_count'] }})</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 border-t border-slate-200 pt-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex flex-wrap items-center gap-4 text-sm font-semibold text-slate-500">
                                        @if (filled($course['lessons'] ?? null))
                                            <span class="inline-flex items-center gap-2">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path d="m10 8 6 4-6 4V8Z" />
                                                </svg>
                                                {{ $course['lessons'] }}
                                            </span>
                                        @endif

                                        @if (filled($course['duration'] ?? null))
                                            <span class="inline-flex items-center gap-2">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path d="M12 7v5l3 2" />
                                                </svg>
                                                {{ $course['duration'] }}
                                            </span>
                                        @endif
                                    </div>

                                    @if (filled($course['button_label'] ?? null))
                                        <a href="#contact"
                                            class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                                            {{ $course['button_label'] }}
                                        </a>
                                    @elseif (filled($course['price'] ?? null))
                                        <span class="text-xl font-black" style="color: {{ $courseColor }}">
                                            {{ $course['price'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
