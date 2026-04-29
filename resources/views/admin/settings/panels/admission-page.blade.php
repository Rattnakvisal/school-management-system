                    <section data-settings-panel="admission-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.admission-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="admission-page">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">Admissions</span>
                                    <h3 class="mt-3 text-base font-black text-slate-900">Admission Section</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="admission[badge]" type="text"
                                            value="{{ old('admission.badge', $admissionSection?->subtitle ?? \App\Support\HomePageContent::text('admission.badge')) }}"
                                            placeholder="Badge"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        <textarea name="admission[title]" rows="2" placeholder="Title"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('admission.title', $admissionSection?->title ?? \App\Support\HomePageContent::text('admission.title')) }}</textarea>
                                        <textarea name="admission[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('admission.description', $admissionSection?->description ?? \App\Support\HomePageContent::text('admission.description')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <h3 class="text-base font-black text-slate-900">Open Intake Card</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="intake[label]" type="text"
                                            value="{{ old('intake.label', $admissionIntake?->subtitle ?? \App\Support\HomePageContent::text('admission.open_intake_label')) }}"
                                            placeholder="Label"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        <input name="intake[title]" type="text"
                                            value="{{ old('intake.title', $admissionIntake?->title ?? \App\Support\HomePageContent::text('admission.open_intake_title')) }}"
                                            placeholder="Title"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        <textarea name="intake[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('intake.description', $admissionIntake?->description ?? \App\Support\HomePageContent::text('admission.open_intake_description')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Admission Steps</h3>
                                        <button type="button" data-add-home-card="admission-steps"
                                            class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white">Add
                                            Step</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $admissionStepLimit; $i++)
                                            @php
                                                $step = $admissionSteps->get($i);
                                                $stepTitle = old(
                                                    'admission_steps.' . $i . '.title',
                                                    $step?->title ?? '',
                                                );
                                                $stepDescription = old(
                                                    'admission_steps.' . $i . '.description',
                                                    $step?->description ?? '',
                                                );
                                                $stepColor = old(
                                                    'admission_steps.' . $i . '.color',
                                                    $iconColorValue(
                                                        $step?->color,
                                                        $admissionStepColorDefaults[
                                                            $i % count($admissionStepColorDefaults)
                                                        ],
                                                    ),
                                                );
                                                $showStep = filled($stepTitle) || filled($stepDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="admission-steps"
                                                class="{{ $showStep ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="admission_steps[{{ $i }}][id]"
                                                    value="{{ $step?->id }}">
                                                <input type="hidden"
                                                    name="admission_steps[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Step
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2">
                                                        <label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200"><input
                                                                type="hidden"
                                                                name="admission_steps[{{ $i }}][active]"
                                                                value="0"><input type="checkbox"
                                                                name="admission_steps[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('admission_steps.' . $i . '.active', $step?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active</label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="admission_steps[{{ $i }}][title]"
                                                    type="text" value="{{ $stepTitle }}" placeholder="Step title"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <textarea name="admission_steps[{{ $i }}][description]" rows="3" placeholder="Step description"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $stepDescription }}</textarea>
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="admission_steps[{{ $i }}][color]"
                                                        type="color" value="{{ $stepColor }}"
                                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl"><button
                                            type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            Admission Page</button></div>
                                </div>
                            </form>
                        @endif
                    </section>
