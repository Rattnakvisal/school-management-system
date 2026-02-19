@extends('layout.guest')

@section('title', 'Email Verification')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-slate-900">Verify your email</h1>
                    <p class="mt-1 text-sm text-slate-600">Before continuing, please check your email for a verification
                        link.</p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                            A new verification link has been sent to your email address.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}" class="mt-6 space-y-4">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-white font-semibold shadow-sm hover:bg-slate-800">Resend
                            verification email</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="text-sm text-slate-600 underline">Log out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
