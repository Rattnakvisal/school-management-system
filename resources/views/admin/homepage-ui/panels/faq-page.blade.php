                    <section data-settings-panel="faq-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.faq-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="faq-page">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">FAQ</span>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="faq[badge]" type="text"
                                            value="{{ old('faq.badge', $faqSection?->subtitle ?? \App\Support\HomePageContent::text('faq.badge')) }}"
                                            placeholder="Badge"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="faq[title]" rows="2" placeholder="Title"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold">{{ old('faq.title', $faqSection?->title ?? \App\Support\HomePageContent::text('faq.title')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <h3 class="text-base font-black text-slate-900">Help Card</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="faq_help[label]" type="text"
                                            value="{{ old('faq_help.label', $faqHelp?->subtitle ?? \App\Support\HomePageContent::text('faq.more_help_label')) }}"
                                            placeholder="Label"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <input name="faq_help[title]" type="text"
                                            value="{{ old('faq_help.title', $faqHelp?->title ?? \App\Support\HomePageContent::text('faq.more_help_title')) }}"
                                            placeholder="Title"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="faq_help[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('faq_help.description', $faqHelp?->description ?? \App\Support\HomePageContent::text('faq.more_help_text')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">FAQ Items</h3><button
                                            type="button" data-add-home-card="faq-items"
                                            class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white">Add
                                            FAQ</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $faqLimit; $i++)
                                            @php
                                                $faq = $dynamicFaqItems->get($i);
                                                $question = old('faq_items.' . $i . '.question', $faq?->title ?? '');
                                                $answer = old('faq_items.' . $i . '.answer', $faq?->description ?? '');
                                                $showFaq = filled($question) || filled($answer);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="faq-items"
                                                class="{{ $showFaq ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="faq_items[{{ $i }}][id]"
                                                    value="{{ $faq?->id }}"><input type="hidden"
                                                    name="faq_items[{{ $i }}][delete]" data-home-delete-field
                                                    value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2"><span
                                                        class="text-xs font-bold text-slate-500">Question
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2"><label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200"><input
                                                                type="hidden"
                                                                name="faq_items[{{ $i }}][active]"
                                                                value="0"><input type="checkbox"
                                                                name="faq_items[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('faq_items.' . $i . '.active', $faq?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active</label><button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="faq_items[{{ $i }}][question]" type="text"
                                                    value="{{ $question }}" placeholder="Question"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <textarea name="faq_items[{{ $i }}][answer]" rows="3" placeholder="Answer"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $answer }}</textarea>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl"><button
                                            type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            FAQ Page</button></div>
                                </div>
                            </form>
                        @endif
                    </section>
