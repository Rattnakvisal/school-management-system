@extends('layout.guest')

@section('title', 'Verify Login OTP')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4 py-10">
        <div class="w-full max-w-md rounded-3xl bg-white p-7 shadow-2xl ring-1 ring-slate-200 sm:p-9">
            <div class="text-center">
                <div class="text-xl font-extrabold tracking-wide text-slate-900">
                    EDU-<span class="text-red-500">X</span>
                </div>
                <h1 class="mt-2 text-2xl font-bold text-slate-900">Verify Login OTP</h1>
                <p class="mt-2 text-sm text-slate-500">
                    We sent a 6-digit OTP to your Telegram linked with phone {{ $maskedPhone }}.
                </p>
            </div>

            @if (session('success'))
                <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
                    {{ session('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.otp.verify') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="otp" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        OTP Code
                    </label>
                    <input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]*"
                        maxlength="6" autocomplete="one-time-code" value="{{ old('otp') }}" required autofocus
                        class="mt-2 w-full rounded-full border border-slate-200 bg-slate-50 px-5 py-3 text-center text-lg tracking-[0.45em] text-slate-900 shadow-sm outline-none focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                        placeholder="000000">
                </div>

                <button type="submit"
                    class="w-full rounded-full bg-indigo-600 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                    Verify OTP
                </button>
            </form>

            <form method="POST" action="{{ route('login.otp.resend') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Resend OTP
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                    Back to login
                </a>
            </div>
        </div>
    </div>
@endsection
