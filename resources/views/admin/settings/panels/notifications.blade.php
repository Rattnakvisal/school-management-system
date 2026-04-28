                    <section data-settings-panel="notifications" class="hidden">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Notifications</h2>
                            <p class="mt-2 text-sm text-slate-600">Unread notifications:
                                <span
                                    class="font-semibold text-indigo-700">{{ number_format($stats['unreadNotifications'] ?? 0) }}</span>
                            </p>
                            <p class="mt-1 text-sm text-slate-500">This section is ready for notification preferences.</p>
                        </div>
                    </section>
