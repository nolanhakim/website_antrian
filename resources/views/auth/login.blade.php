@extends('layouts.app')

@section('title', 'Masuk — antrianQ')

@section('content')
    <div class="mx-auto flex min-h-dvh w-full max-w-sm flex-col justify-center px-6 py-12">
        <div class="rounded-2xl border border-line bg-surface p-8 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <span class="grid size-8 place-items-center rounded-xl bg-accent text-sm font-bold text-white shadow-xs">Q</span>
                <span class="text-sm font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
            </div>

            <h1 class="mt-6 text-xl font-semibold tracking-tight text-ink">Masuk untuk staf</h1>
            <p class="mt-1 text-xs text-muted">Akses panel operator loket dan dashboard pemilik.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <label class="block">
                    <span class="text-xs font-semibold text-muted">Email</span>
                    <input
                        type="email"
                        name="email"
                        required
                        autofocus
                        value="{{ old('email') }}"
                        placeholder="nama@antrianq.test"
                        class="mt-1.5 w-full rounded-xl border border-line bg-canvas/40 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15"
                    >
                </label>

                <label class="block">
                    <span class="text-xs font-semibold text-muted">Password</span>
                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="mt-1.5 w-full rounded-xl border border-line bg-canvas/40 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15"
                    >
                </label>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-muted cursor-pointer">
                        <input type="checkbox" name="remember" class="size-4 rounded-md border-line accent-accent">
                        <span>Ingat saya</span>
                    </label>
                </div>

                @error('email')
                    <p class="text-xs font-medium text-skip">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    class="w-full rounded-xl bg-accent px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-accent-hover active:scale-[0.98]"
                >
                    Masuk
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('kiosk') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-muted hover:text-ink transition">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                <span>Kembali ke ambil antrean</span>
            </a>
        </div>
    </div>
@endsection
