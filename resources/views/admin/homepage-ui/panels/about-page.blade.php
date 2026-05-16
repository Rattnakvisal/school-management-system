<section data-settings-panel="about-page" class="hidden space-y-7">
    @if ($homePageReady ?? false)
        <form method="POST" action="{{ route('admin.settings.about-page.update') }}" enctype="multipart/form-data"
            class="space-y-7">
            @csrf
            @method('PUT')
            <input type="hidden" name="_settings_tab" value="about-page">
            <input type="hidden" id="remove_about_image" name="about[remove_image]" value="0">

            {{-- ── About Content ── --}}
            <div
                class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                {{-- Decorative accent bar --}}
                <div
                    class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-cyan-400 via-sky-500 to-indigo-500 rounded-t-3xl">
                </div>

                <div class="px-6 pb-6 pt-7 sm:px-8 sm:pb-8 sm:pt-9">
                    {{-- Header --}}
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="space-y-2">
                            <div
                                class="inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-cyan-50 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-cyan-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                                About Section
                            </div>
                            <h3 class="text-xl font-black tracking-tight text-slate-900">About Content</h3>
                            <p class="text-[13px] leading-relaxed text-slate-500">
                                Main introduction text, image, highlight cards, checklist items, and the green bottom strip.
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3.5 py-1.5 text-[11px] font-bold text-slate-500">
                            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none">
                                <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M6 4v2.5l1.5 1" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            Visible on homepage
                        </span>
                    </div>

                    {{-- Fields --}}
                    <div class="mt-7 grid gap-5 md:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="home_about_badge"
                                class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                Badge
                            </label>
                            <input id="home_about_badge" name="about[badge]" type="text"
                                data-home-preview-target="about_preview_badge"
                                value="{{ old('about.badge', $homeAbout?->subtitle ?? 'About TechBridge') }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition-all duration-200 placeholder:text-slate-300 hover:border-slate-300 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                        </div>
                        <div class="space-y-1.5">
                            <label for="home_about_title"
                                class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                Headline
                            </label>
                            <input id="home_about_title" name="about[title]" type="text"
                                data-home-preview-target="about_preview_title"
                                value="{{ old('about.title', $homeAbout?->title ?? \App\Support\HomePageContent::text('about.title')) }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition-all duration-200 placeholder:text-slate-300 hover:border-slate-300 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="home_about_description"
                                class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                Description
                            </label>
                            <textarea id="home_about_description" name="about[description]" rows="4"
                                data-home-preview-target="about_preview_description"
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm leading-relaxed text-slate-700 outline-none transition-all duration-200 placeholder:text-slate-300 hover:border-slate-300 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">{{ old('about.description', $homeAbout?->description ?? \App\Support\HomePageContent::text('about.description')) }}</textarea>
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                About Image
                            </label>
                            <input id="about_image_input" type="file" name="about[image]" accept="image/*"
                                class="hidden">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" id="upload_about_image_btn"
                                    class="rounded-xl bg-slate-900 px-4 py-3 text-[12px] font-bold text-white transition-all duration-150 hover:bg-slate-800 active:scale-95">
                                    Upload Image
                                </button>
                                <button type="button" id="delete_about_image_btn"
                                    class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[12px] font-bold text-slate-700 transition-all duration-150 hover:bg-slate-50 active:scale-95">
                                    Use Default
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-emerald-100 bg-[#effff9]">
                        <div class="grid gap-0 lg:grid-cols-[180px_minmax(0,1fr)]">
                            <div class="relative min-h-44 overflow-hidden bg-emerald-50">
                                <div class="absolute bottom-0 left-0 right-0 h-12 bg-emerald-400/15"></div>
                                <img id="about_image_preview" src="{{ $aboutImage }}" alt="About preview"
                                    class="relative z-10 h-48 w-full object-contain object-bottom p-2">
                            </div>
                            <div class="p-5">
                                <p id="about_preview_badge"
                                    class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600">
                                    {{ old('about.badge', $homeAbout?->subtitle ?? 'About TechBridge') }}
                                </p>
                                <h4 id="about_preview_title"
                                    class="mt-2 text-xl font-black leading-tight tracking-tight text-[#10221e]">
                                    {{ old('about.title', $homeAbout?->title ?? \App\Support\HomePageContent::text('about.title')) }}
                                </h4>
                                <p id="about_preview_description"
                                    class="mt-3 line-clamp-4 text-[12px] leading-5 text-slate-600">
                                    {{ old('about.description', $homeAbout?->description ?? \App\Support\HomePageContent::text('about.description')) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Highlights + Cards ── --}}
            <div class="grid gap-6 xl:grid-cols-2">

                {{-- About Highlights --}}
                <div
                    class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                    <div
                        class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-cyan-400 to-sky-500 rounded-t-3xl">
                    </div>

                    <div class="px-6 pb-6 pt-7 sm:px-7">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <h3 class="text-base font-black tracking-tight text-slate-900">About Highlights</h3>
                                <p class="text-[12px] leading-relaxed text-slate-500">Highlight cards shown below the
                                    About introduction text.</p>
                            </div>
                            <button type="button" data-add-home-card="about-highlights"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-cyan-600 px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm shadow-cyan-200 transition-all duration-150 hover:bg-cyan-500 hover:shadow-cyan-300 active:scale-95">
                                <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <path d="M6 1v10M1 6h10" />
                                </svg>
                                Add Highlight
                            </button>
                        </div>

                        <div class="mt-5 space-y-3">
                            @for ($i = 0; $i < $aboutHighlightLimit; $i++)
                                @php
                                    $aboutHighlightItem = $homeAboutHighlights->get($i);
                                    $aboutHighlightTitle = old(
                                        'about_highlights.' . $i . '.title',
                                        $aboutHighlightItem?->title ?? '',
                                    );
                                    $aboutHighlightDescription = old(
                                        'about_highlights.' . $i . '.description',
                                        $aboutHighlightItem?->description ?? '',
                                    );
                                    $aboutHighlightIcon = old(
                                        'about_highlights.' . $i . '.icon',
                                        $aboutHighlightItem?->icon ?? '',
                                    );
                                    $aboutHighlightColor = old(
                                        'about_highlights.' . $i . '.color',
                                        $iconColorValue(
                                            $aboutHighlightItem?->color,
                                            $aboutHighlightColorDefaults[$i % count($aboutHighlightColorDefaults)],
                                        ),
                                    );
                                    $showAboutHighlight =
                                        filled($aboutHighlightTitle) || filled($aboutHighlightDescription);
                                @endphp

                                <div data-home-editor-card data-addable-card="about-highlights"
                                    class="{{ $showAboutHighlight ? '' : 'hidden' }} group/card rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 transition-shadow duration-200 hover:shadow-md">
                                    <input type="hidden" name="about_highlights[{{ $i }}][id]"
                                        value="{{ $aboutHighlightItem?->id }}">
                                    <input type="hidden" name="about_highlights[{{ $i }}][delete]"
                                        data-home-delete-field value="0">

                                    {{-- Card header --}}
                                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                        <span
                                            class="flex items-center gap-2 text-[11px] font-black uppercase tracking-wide text-slate-400">
                                            <span
                                                class="grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-cyan-500 to-sky-600 text-[10px] font-black text-white shadow-sm">
                                                {{ $i + 1 }}
                                            </span>
                                            Highlight {{ $i + 1 }}
                                        </span>
                                        <div data-home-card-actions class="flex flex-wrap items-center gap-1.5">
                                            <button type="button" data-home-edit-card
                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-[11px] font-bold text-blue-600 transition-all duration-150 hover:border-blue-300 hover:bg-blue-50 active:scale-95">
                                                Edit
                                            </button>
                                            <label
                                                class="inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-bold text-slate-600 transition-colors hover:bg-slate-50">
                                                <input type="hidden"
                                                    name="about_highlights[{{ $i }}][active]"
                                                    value="0">
                                                <input type="checkbox"
                                                    name="about_highlights[{{ $i }}][active]"
                                                    data-home-active-toggle value="1"
                                                    {{ old('about_highlights.' . $i . '.active', $aboutHighlightItem?->is_active ?? true) ? 'checked' : '' }}
                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                Active
                                            </label>
                                            <button type="button" data-home-remove-card
                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-[11px] font-bold text-red-500 transition-all duration-150 hover:border-red-300 hover:bg-red-50 active:scale-95">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Fields --}}
                                    <div class="space-y-2">
                                        <input name="about_highlights[{{ $i }}][title]" type="text"
                                            placeholder="Highlight title" value="{{ $aboutHighlightTitle }}"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-900 outline-none transition-all placeholder:font-normal placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                        <textarea name="about_highlights[{{ $i }}][description]" rows="3"
                                            placeholder="Highlight description"
                                            class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm leading-relaxed text-slate-700 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">{{ $aboutHighlightDescription }}</textarea>
                                        <input name="about_highlights[{{ $i }}][icon]" type="text"
                                            data-random-icon-input
                                            placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                            value="{{ $aboutHighlightIcon }}"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                        <label
                                            class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[11px] font-bold text-slate-500">
                                            <span class="flex-1">Icon color</span>
                                            <input name="about_highlights[{{ $i }}][color]" type="color"
                                                value="{{ $aboutHighlightColor }}"
                                                class="h-8 w-10 cursor-pointer rounded-lg border border-slate-200 bg-white p-0.5">
                                        </label>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- About Cards --}}
                <div
                    class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                    <div
                        class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-emerald-400 to-teal-500 rounded-t-3xl">
                    </div>

                    <div class="px-6 pb-6 pt-7 sm:px-7">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <h3 class="text-base font-black tracking-tight text-slate-900">About Cards</h3>
                                <p class="text-[12px] leading-relaxed text-slate-500">Checklist items and bottom strip
                                    labels in the About section.</p>
                            </div>
                            <button type="button" data-add-home-card="about-cards"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-600 px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm shadow-emerald-200 transition-all duration-150 hover:bg-emerald-500 hover:shadow-emerald-300 active:scale-95">
                                <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <path d="M6 1v10M1 6h10" />
                                </svg>
                                Add Card
                            </button>
                        </div>

                        <div class="mt-5 space-y-3">
                            @for ($i = 0; $i < $aboutCardLimit; $i++)
                                @php
                                    $aboutCardItem = $homeAboutCards->get($i);
                                    $aboutCardTitle = old('about_cards.' . $i . '.title', $aboutCardItem?->title ?? '');
                                    $aboutCardDescription = old(
                                        'about_cards.' . $i . '.description',
                                        $aboutCardItem?->description ?? '',
                                    );
                                    $aboutCardIcon = old('about_cards.' . $i . '.icon', $aboutCardItem?->icon ?? '');
                                    $aboutCardColor = old(
                                        'about_cards.' . $i . '.color',
                                        $iconColorValue(
                                            $aboutCardItem?->color,
                                            $aboutCardColorDefaults[$i % count($aboutCardColorDefaults)],
                                        ),
                                    );
                                    $showAboutCard = filled($aboutCardTitle) || filled($aboutCardDescription);
                                @endphp

                                <div data-home-editor-card data-addable-card="about-cards"
                                    class="{{ $showAboutCard ? '' : 'hidden' }} group/card rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 transition-shadow duration-200 hover:shadow-md">
                                    <input type="hidden" name="about_cards[{{ $i }}][id]"
                                        value="{{ $aboutCardItem?->id }}">
                                    <input type="hidden" name="about_cards[{{ $i }}][delete]"
                                        data-home-delete-field value="0">

                                    {{-- Card header --}}
                                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                        <span
                                            class="flex items-center gap-2 text-[11px] font-black uppercase tracking-wide text-slate-400">
                                            <span
                                                class="grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-[10px] font-black text-white shadow-sm">
                                                {{ $i + 1 }}
                                            </span>
                                            About card {{ $i + 1 }}
                                        </span>
                                        <div data-home-card-actions class="flex flex-wrap items-center gap-1.5">
                                            <button type="button" data-home-edit-card
                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-[11px] font-bold text-blue-600 transition-all duration-150 hover:border-blue-300 hover:bg-blue-50 active:scale-95">
                                                Edit
                                            </button>
                                            <label
                                                class="inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-bold text-slate-600 transition-colors hover:bg-slate-50">
                                                <input type="hidden" name="about_cards[{{ $i }}][active]"
                                                    value="0">
                                                <input type="checkbox"
                                                    name="about_cards[{{ $i }}][active]"
                                                    data-home-active-toggle value="1"
                                                    {{ old('about_cards.' . $i . '.active', $aboutCardItem?->is_active ?? true) ? 'checked' : '' }}
                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                Active
                                            </label>
                                            <button type="button" data-home-remove-card
                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-[11px] font-bold text-red-500 transition-all duration-150 hover:border-red-300 hover:bg-red-50 active:scale-95">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Fields --}}
                                    <div class="space-y-2">
                                        <input name="about_cards[{{ $i }}][title]" type="text"
                                            placeholder="Card title" value="{{ $aboutCardTitle }}"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-900 outline-none transition-all placeholder:font-normal placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                        <textarea name="about_cards[{{ $i }}][description]" rows="3" placeholder="Card description"
                                            class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm leading-relaxed text-slate-700 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">{{ $aboutCardDescription }}</textarea>
                                        <input name="about_cards[{{ $i }}][icon]" type="text"
                                            data-random-icon-input
                                            placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                            value="{{ $aboutCardIcon }}"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                        <label
                                            class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[11px] font-bold text-slate-500">
                                            <span class="flex-1">Icon color</span>
                                            <input name="about_cards[{{ $i }}][color]" type="color"
                                                value="{{ $aboutCardColor }}"
                                                class="h-8 w-10 cursor-pointer rounded-lg border border-slate-200 bg-white p-0.5">
                                        </label>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Sticky Save Bar ── --}}
            <div class="sticky bottom-5 z-10 flex justify-end">
                <div
                    class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/90 px-3 py-2.5 shadow-xl shadow-slate-200/60 backdrop-blur-md">
                    <p class="hidden text-[11px] font-semibold text-slate-400 sm:block">Unsaved changes</p>
                    <div class="hidden h-4 w-px bg-slate-200 sm:block"></div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition-all duration-150 hover:bg-indigo-500 hover:shadow-indigo-300 active:scale-[0.97]">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13.5 4.5L6 12 2.5 8.5" />
                        </svg>
                        Save About Page
                    </button>
                </div>
            </div>
        </form>
    @else
        {{-- Migration notice --}}
        <div class="flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/20 dark:border-amber-900/50 dark:bg-amber-900/20 p-5 text-sm text-amber-800">
            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full bg-amber-100 text-amber-600">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <path d="M8 1v6m0 3v.5" />
                    <circle cx="8" cy="8" r="7" />
                </svg>
            </span>
            <div>
                <p class="font-bold text-amber-900">Migrations required</p>
                <p class="mt-0.5 text-amber-700 dark:text-amber-300">Run migrations to enable dynamic about page editing.</p>
            </div>
        </div>
    @endif
</section>
