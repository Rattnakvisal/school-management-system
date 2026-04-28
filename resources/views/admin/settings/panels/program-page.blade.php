                    <section data-settings-panel="program-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.program-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="program-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-700">
                                                Academic programs
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">Program Section Header
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500">Badge, headline, and description shown
                                                above the program cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="program_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="program_badge" name="program[badge]" type="text"
                                                value="{{ old('program.badge', $programSection?->subtitle ?? __('home.programs.badge')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="program_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <textarea id="program_title" name="program[title]" rows="2"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold leading-6 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('program.title', $programSection?->title ?? __('home.programs.title')) }}</textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="program_description"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                                            <textarea id="program_description" name="program[description]" rows="3"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('program.description', $programSection?->description ?? __('home.programs.description')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Program Cards</h3>
                                            <p class="mt-1 text-xs text-slate-500">Academic programs displayed in the
                                                homepage Academic Programs section.</p>
                                        </div>
                                        <button type="button" data-add-home-card="program-cards"
                                            class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-indigo-500">
                                            Add Program
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $programLimit; $i++)
                                            @php
                                                $programItem = $programCards->get($i);
                                                $programLevel = old(
                                                    'program_cards.' . $i . '.level',
                                                    $programItem?->subtitle ?? '',
                                                );
                                                $programTitle = old(
                                                    'program_cards.' . $i . '.title',
                                                    $programItem?->title ?? '',
                                                );
                                                $programDescription = old(
                                                    'program_cards.' . $i . '.description',
                                                    $programItem?->description ?? '',
                                                );
                                                $programIcon = old(
                                                    'program_cards.' . $i . '.icon',
                                                    $programItem?->icon ?? '',
                                                );
                                                $programColor = old(
                                                    'program_cards.' . $i . '.color',
                                                    $iconColorValue(
                                                        $programItem?->color,
                                                        $programCardColorDefaults[
                                                            $i % count($programCardColorDefaults)
                                                        ],
                                                    ),
                                                );
                                                $showProgram =
                                                    filled($programLevel) ||
                                                    filled($programTitle) ||
                                                    filled($programDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="program-cards"
                                                class="{{ $showProgram ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="program_cards[{{ $i }}][id]"
                                                    value="{{ $programItem?->id }}">
                                                <input type="hidden" name="program_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-indigo-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Program card {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="program_cards[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="program_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('program_cards.' . $i . '.active', $programItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="program_cards[{{ $i }}][level]" type="text"
                                                    placeholder="Level, example: Primary" value="{{ $programLevel }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <input name="program_cards[{{ $i }}][title]" type="text"
                                                    placeholder="Program title" value="{{ $programTitle }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="program_cards[{{ $i }}][description]" rows="3" placeholder="Program description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $programDescription }}</textarea>
                                                <input name="program_cards[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-book-open"></i>, or SVG'
                                                    value="{{ $programIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="program_cards[{{ $i }}][color]"
                                                        type="color" value="{{ $programColor }}"
                                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Program Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic program page editing.
                            </div>
                        @endif
                    </section>
