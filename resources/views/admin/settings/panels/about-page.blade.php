                    <section data-settings-panel="about-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.about-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="about-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">
                                                About section
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">About Content</h3>
                                            <p class="mt-1 text-xs text-slate-500">Main About text, highlights, mission,
                                                vision, culture, and operations cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="home_about_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="home_about_badge" name="about[badge]" type="text"
                                                value="{{ old('about.badge', $homeAbout?->subtitle ?? 'About TechBridge') }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div>
                                            <label for="home_about_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <input id="home_about_title" name="about[title]" type="text"
                                                value="{{ old('about.title', $homeAbout?->title ?? \App\Support\HomePageContent::text('about.title')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="home_about_description"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                                            <textarea id="home_about_description" name="about[description]" rows="4"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('about.description', $homeAbout?->description ?? \App\Support\HomePageContent::text('about.description')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid gap-5 xl:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">About Highlights</h3>
                                                <p class="mt-1 text-xs text-slate-500">Small cards inside the dark
                                                    About panel.</p>
                                            </div>
                                            <button type="button" data-add-home-card="about-highlights"
                                                class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-cyan-500">
                                                Add Highlight
                                            </button>
                                        </div>
                                        <div class="mt-4 space-y-3">
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
                                                            $aboutHighlightColorDefaults[
                                                                $i % count($aboutHighlightColorDefaults)
                                                            ],
                                                        ),
                                                    );
                                                    $showAboutHighlight =
                                                        filled($aboutHighlightTitle) ||
                                                        filled($aboutHighlightDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="about-highlights"
                                                    class="{{ $showAboutHighlight ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden"
                                                        name="about_highlights[{{ $i }}][id]"
                                                        value="{{ $aboutHighlightItem?->id }}">
                                                    <input type="hidden"
                                                        name="about_highlights[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-cyan-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Highlight {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
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
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input name="about_highlights[{{ $i }}][title]"
                                                        type="text" placeholder="Highlight title"
                                                        value="{{ $aboutHighlightTitle }}"
                                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <textarea name="about_highlights[{{ $i }}][description]" rows="3" placeholder="Highlight description"
                                                        class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $aboutHighlightDescription }}</textarea>
                                                    <input name="about_highlights[{{ $i }}][icon]"
                                                        type="text" data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $aboutHighlightIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <label
                                                        class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                        <span class="min-w-0 flex-1">Icon color</span>
                                                        <input name="about_highlights[{{ $i }}][color]"
                                                            type="color" value="{{ $aboutHighlightColor }}"
                                                            class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">About Cards</h3>
                                                <p class="mt-1 text-xs text-slate-500">Cards shown on the right side of
                                                    the
                                                    About section.</p>
                                            </div>
                                            <button type="button" data-add-home-card="about-cards"
                                                class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                Add Card
                                            </button>
                                        </div>
                                        <div class="mt-4 grid gap-3">
                                            @for ($i = 0; $i < $aboutCardLimit; $i++)
                                                @php
                                                    $aboutCardItem = $homeAboutCards->get($i);
                                                    $aboutCardTitle = old(
                                                        'about_cards.' . $i . '.title',
                                                        $aboutCardItem?->title ?? '',
                                                    );
                                                    $aboutCardDescription = old(
                                                        'about_cards.' . $i . '.description',
                                                        $aboutCardItem?->description ?? '',
                                                    );
                                                    $aboutCardIcon = old(
                                                        'about_cards.' . $i . '.icon',
                                                        $aboutCardItem?->icon ?? '',
                                                    );
                                                    $aboutCardColor = old(
                                                        'about_cards.' . $i . '.color',
                                                        $iconColorValue(
                                                            $aboutCardItem?->color,
                                                            $aboutCardColorDefaults[
                                                                $i % count($aboutCardColorDefaults)
                                                            ],
                                                        ),
                                                    );
                                                    $showAboutCard =
                                                        filled($aboutCardTitle) || filled($aboutCardDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="about-cards"
                                                    class="{{ $showAboutCard ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="about_cards[{{ $i }}][id]"
                                                        value="{{ $aboutCardItem?->id }}">
                                                    <input type="hidden"
                                                        name="about_cards[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div
                                                        class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-emerald-500 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            About card {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="about_cards[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="about_cards[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('about_cards.' . $i . '.active', $aboutCardItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input name="about_cards[{{ $i }}][title]"
                                                        type="text" placeholder="Card title"
                                                        value="{{ $aboutCardTitle }}"
                                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <textarea name="about_cards[{{ $i }}][description]" rows="3" placeholder="Card description"
                                                        class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $aboutCardDescription }}</textarea>
                                                    <input name="about_cards[{{ $i }}][icon]"
                                                        type="text" data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $aboutCardIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <label
                                                        class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                        <span class="min-w-0 flex-1">Icon color</span>
                                                        <input name="about_cards[{{ $i }}][color]"
                                                            type="color" value="{{ $aboutCardColor }}"
                                                            class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save About Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic about page editing.
                            </div>
                        @endif
                    </section>
