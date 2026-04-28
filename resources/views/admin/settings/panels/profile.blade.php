                    <section data-settings-panel="profile" class="space-y-6">
                        <form method="POST" action="{{ route('admin.settings.profile.update') }}"
                            enctype="multipart/form-data"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="remove_admin_avatar" name="remove_avatar" value="0">

                            <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 pb-5">
                                <div class="relative">
                                    <img id="admin_avatar_preview"
                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-sm"
                                        src="{{ $admin->avatar_url }}" data-fallback="{{ $admin->fallback_avatar_url }}"
                                        onerror="this.onerror=null;this.src='{{ $admin->fallback_avatar_url }}';"
                                        alt="admin avatar">
                                    <button type="button" id="trigger_avatar_upload"
                                        class="absolute -bottom-1 -right-1 grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-indigo-600 text-white shadow transition hover:bg-indigo-500"
                                        aria-label="Upload avatar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                                            <path
                                                d="M20 4h-3.17L15.6 2.35A2 2 0 0 0 14.06 2H9.94A2 2 0 0 0 8.4 2.35L7.17 4H4a2 2 0 0 0-2 2v10a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4V6a2 2 0 0 0-2-2Zm-8 13a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <input id="admin_avatar_input" type="file" name="avatar" accept="image/*"
                                        class="hidden">
                                    <button type="button" id="upload_avatar_btn"
                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">
                                        Upload New
                                    </button>
                                    <button type="button" id="delete_avatar_btn"
                                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        Delete avatar
                                    </button>
                                    <span class="text-xs text-slate-500">JPG, PNG, WEBP up to 4MB.</span>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="first_name" class="mb-1 block text-xs font-semibold text-slate-600">First
                                        Name
                                        <span class="text-red-500">*</span></label>
                                    <input id="first_name" name="first_name" type="text"
                                        value="{{ old('first_name', $defaultFirstName) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('first_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="mb-1 block text-xs font-semibold text-slate-600">Last
                                        Name</label>
                                    <input id="last_name" name="last_name" type="text"
                                        value="{{ old('last_name', $defaultLastName) }}"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('last_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                    <input id="email" name="email" type="email"
                                        value="{{ old('email', $admin->email) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('email', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone_number"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Mobile
                                        Number</label>
                                    <input id="phone_number" name="phone_number" type="text"
                                        value="{{ old('phone_number', $admin->phone_number) }}"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                        placeholder="+855 ...">
                                    @error('phone_number', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                                    <input type="text" readonly value="{{ ucfirst((string) $admin->role) }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">ID</label>
                                    <input type="text" readonly value="ID #{{ $admin->formatted_id }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </section>
