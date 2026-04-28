                    <section data-settings-panel="password" class="hidden space-y-6">
                        <form method="POST" action="{{ route('admin.settings.password.update') }}"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-semibold text-slate-900">Change Password</h2>
                            <p class="mt-1 text-sm text-slate-500">Use a strong password with at least 8 characters.</p>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="current_password"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Current
                                        Password</label>
                                    <input id="current_password" type="password" name="current_password" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('current_password', 'passwordUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="password" class="mb-1 block text-xs font-semibold text-slate-600">New
                                            Password</label>
                                        <input id="password" type="password" name="password" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        @error('password', 'passwordUpdate')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation"
                                            class="mb-1 block text-xs font-semibold text-slate-600">Confirm
                                            Password</label>
                                        <input id="password_confirmation" type="password" name="password_confirmation"
                                            required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </section>
