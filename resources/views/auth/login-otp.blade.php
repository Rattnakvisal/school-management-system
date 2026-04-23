@extends('layout.guest')

@section('title', 'Verify Login OTP')

@section('content')
    <div
        class="min-h-screen bg-[linear-gradient(135deg,#eef2ff_0%,#f8fafc_45%,#e0e7ff_100%)] px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-4">
        <div
            class="mx-auto flex max-w-5xl overflow-hidden rounded-[34px] border border-white/60 bg-white shadow-[0_30px_80px_rgba(99,102,241,0.16)] lg:h-[calc(100vh-2rem)] lg:max-h-[920px]">
            <section class="flex w-full items-center bg-white lg:w-[46%] lg:min-h-0">
                <div class="mx-auto flex w-full max-w-md flex-col justify-center px-6 py-8 sm:px-10 sm:py-10 lg:px-10 lg:py-6 xl:px-12">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/techbridge-logo-mark.svg') }}" alt="TechBridge Academy logo"
                            class="h-14 w-14 object-contain drop-shadow-[0_16px_34px_rgba(24,80,200,0.22)]" />
                        <div>
                            <p class="text-xl font-extrabold tracking-[-0.03em] text-slate-900">TechBridge</p>
                            <p class="text-xs font-semibold uppercase tracking-[0.26em] text-amber-500">Academy</p>
                        </div>
                    </div>

                    <div class="mt-6 lg:mt-5">
                        <h1 class="font-['Manrope'] text-4xl font-extrabold tracking-[-0.05em] text-slate-900 sm:text-5xl">
                            Verify OTP
                        </h1>
                        <p class="mt-3 max-w-sm text-sm leading-6 text-slate-500">
                            We sent a 6-digit Telegram login code to the phone number linked with
                            <span class="font-semibold text-slate-700">{{ $maskedPhone }}</span>.
                        </p>
                    </div>

                    @if (session('success'))
                        <div
                            class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <ul class="space-y-1 pl-5 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="my-6 flex items-center gap-4 lg:my-5">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Secure verification</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <form method="POST" action="{{ route('login.otp.verify') }}" class="space-y-4 lg:space-y-3">
                        @csrf

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <label for="otp" class="block text-sm font-semibold text-slate-700">OTP Code</label>
                                <span class="text-xs font-medium text-slate-400">6 digits</span>
                            </div>

                            <div class="relative mt-2">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-5 flex items-center text-indigo-500">
                                    <span
                                        class="relative flex h-9 w-9 items-center justify-center rounded-full bg-indigo-50 shadow-[0_10px_22px_rgba(79,70,229,0.16)]">
                                        <span
                                            class="absolute inset-0 rounded-full bg-indigo-200/60 opacity-70 animate-ping"></span>
                                        <svg class="relative h-5 w-5 animate-bounce" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 10h.01M12 10h.01M16 10h.01M7 16l-4 4V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H7z" />
                                        </svg>
                                    </span>
                                </div>
                                <input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                                    autocomplete="one-time-code" value="{{ old('otp') }}" required autofocus
                                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-20 pr-6 text-center text-2xl font-bold tracking-[0.48em] text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 lg:py-2.5"
                                    placeholder="000000">
                            </div>
                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Enter the one-time password from your Telegram message to complete sign in.
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-[linear-gradient(135deg,#4f46e5_0%,#6366f1_100%)] px-6 py-3 text-sm font-bold text-white shadow-[0_20px_40px_rgba(79,70,229,0.30)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_50px_rgba(79,70,229,0.34)] focus:outline-none focus:ring-4 focus:ring-indigo-200">
                            Verify OTP
                        </button>
                    </form>

                    <form method="POST" action="{{ route('login.otp.resend') }}" class="mt-3">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-[0_14px_30px_rgba(15,23,42,0.06)] transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-[0_20px_36px_rgba(15,23,42,0.10)] focus:outline-none focus:ring-4 focus:ring-slate-100">
                            Resend OTP
                        </button>
                    </form>

                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-500">
                        Didn&apos;t receive the code yet? Wait a moment, then use
                        <span class="font-semibold text-slate-700">Resend OTP</span>
                        to request a new Telegram message.
                    </div>

                    <p class="mt-4 text-center text-sm text-slate-500">
                        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                            Back to login
                        </a>
                    </p>
                </div>
            </section>

            <aside
                class="relative hidden overflow-hidden bg-[linear-gradient(180deg,#eef2ff_0%,#e8edff_55%,#e3eafe_100%)] lg:flex lg:w-[54%] lg:min-h-0">
                <div class="absolute inset-y-0 left-0 w-px bg-white/70"></div>
                <div class="absolute right-[-80px] top-[-90px] h-72 w-72 rounded-full bg-white/50 blur-3xl"></div>
                <div class="absolute bottom-[-100px] left-[-60px] h-72 w-72 rounded-full bg-indigo-200/30 blur-3xl"></div>

                <div class="relative flex w-full flex-col justify-between px-8 py-7 xl:px-12 xl:py-9">
                    <div class="flex items-start justify-end">
                        <div class="max-w-xs text-right">
                            <div class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <img src="{{ asset('images/techbridge-logo-mark.svg') }}" alt="TechBridge Academy logo"
                                    class="h-5 w-5 object-contain" />
                                TechBridge Academy
                            </div>
                            <p class="mt-5 text-sm leading-6 text-slate-500">
                                Secure sign-in keeps your student, teacher, staff, and admin access protected every day.
                            </p>
                            <div class="mt-5">
                                <span
                                    class="inline-flex rounded-full border border-slate-300 bg-white/80 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-700">
                                    Verify Access
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="relative mx-auto flex w-full max-w-xl flex-1 items-end justify-center pt-6">
                        <div
                            class="absolute bottom-8 left-1/2 h-20 w-[72%] -translate-x-1/2 rounded-full bg-[radial-gradient(circle,rgba(99,102,241,0.18)_0%,rgba(99,102,241,0)_72%)] blur-2xl">
                        </div>
                        <img src="{{ asset('images/8865364.png') }}" alt="OTP illustration"
                            class="relative w-full max-w-lg object-contain drop-shadow-[0_28px_35px_rgba(99,102,241,0.16)] lg:max-h-[48vh] xl:max-h-[54vh]" />
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
