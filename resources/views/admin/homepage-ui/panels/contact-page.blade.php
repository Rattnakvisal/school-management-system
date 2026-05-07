                    <section data-settings-panel="contact-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.contact-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="contact-page">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">Contact
                                        Admissions</span>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="contact[badge]" type="text"
                                            value="{{ old('contact.badge', $contactSection?->subtitle ?? \App\Support\HomePageContent::text('contact.badge')) }}"
                                            placeholder="Badge"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="contact[title]" rows="2" placeholder="Title"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold">{{ old('contact.title', $contactSection?->title ?? \App\Support\HomePageContent::text('contact.title')) }}</textarea>
                                        <textarea name="contact[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('contact.description', $contactSection?->description ?? \App\Support\HomePageContent::text('contact.description')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <h3 class="text-base font-black text-slate-900">Campus Information Card</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="campus[label]" type="text"
                                            value="{{ old('campus.label', $contactCampus?->subtitle ?? \App\Support\HomePageContent::text('contact.campus_label')) }}"
                                            placeholder="Label"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <input name="campus[title]" type="text"
                                            value="{{ old('campus.title', $contactCampus?->title ?? \App\Support\HomePageContent::text('contact.campus_title')) }}"
                                            placeholder="Title"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="campus[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('campus.description', $contactCampus?->description ?? \App\Support\HomePageContent::text('contact.campus_text')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Contact Cards</h3><button
                                            type="button" data-add-home-card="contact-cards"
                                            class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white">Add
                                            Contact</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $contactCardLimit; $i++)
                                            @php
                                                $card = $dynamicContactCards->get($i);
                                                $label = old('contact_cards.' . $i . '.label', $card?->title ?? '');
                                                $value = old('contact_cards.' . $i . '.value', $card?->value ?? '');
                                                $showCard = filled($label) || filled($value);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="contact-cards"
                                                class="{{ $showCard ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="contact_cards[{{ $i }}][id]"
                                                    value="{{ $card?->id }}"><input type="hidden"
                                                    name="contact_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2"><span
                                                        class="text-xs font-bold text-slate-500">Contact card
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2"><label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200"><input
                                                                type="hidden"
                                                                name="contact_cards[{{ $i }}][active]"
                                                                value="0"><input type="checkbox"
                                                                name="contact_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('contact_cards.' . $i . '.active', $card?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active</label><button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="contact_cards[{{ $i }}][label]" type="text"
                                                    value="{{ $label }}" placeholder="Label"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <input name="contact_cards[{{ $i }}][value]" type="text"
                                                    value="{{ $value }}" placeholder="Value"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl"><button
                                            type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            Contact Page</button></div>
                                </div>
                            </form>
                        @endif
                    </section>
