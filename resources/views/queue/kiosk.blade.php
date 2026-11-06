@extends('layouts.app')

@section('title', 'Ambil Nomor Antrean — antrianQ')

@section('content')
    <div class="mx-auto flex min-h-dvh w-full max-w-lg flex-col justify-center px-6 py-12">
        <header>
            <div class="flex items-center gap-2.5">
                <span class="grid size-8 place-items-center rounded-xl bg-accent text-sm font-bold text-white shadow-xs">Q</span>
                <span class="text-base font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
            </div>

            <h1 class="mt-8 text-3xl font-semibold tracking-tight text-ink">
                Ambil nomor antrean
            </h1>
            <p class="mt-2 text-sm text-muted">
                Pilih kategori layanan di bawah untuk mendapatkan nomor antrean Anda.
            </p>
        </header>

        <form method="POST" action="{{ route('queue.store') }}" class="mt-8 space-y-6">
            @csrf

            <label class="block">
                <span class="text-xs font-semibold text-muted">Nama Pelanggan <span class="font-normal text-muted-light">(opsional)</span></span>
                <input
                    type="text"
                    name="customer_name"
                    maxlength="50"
                    value="{{ old('customer_name') }}"
                    placeholder="Masukkan nama Anda"
                    class="mt-1.5 w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:ring-3 focus:ring-accent/15"
                >
            </label>

            @error('prefix')
                <p class="text-xs font-medium text-skip">{{ $message }}</p>
            @enderror

            <fieldset class="space-y-3">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted">Pilih Layanan</legend>

                @foreach ($categories as $prefix => $label)
                    <button
                        type="submit"
                        name="prefix"
                        value="{{ $prefix }}"
                        class="group flex w-full items-center justify-between rounded-2xl border border-line bg-surface p-4 text-left shadow-2xs transition hover:border-accent hover:bg-accent-soft/20 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent/20 active:scale-[0.99]"
                    >
                        <div class="flex items-center gap-4">
                            <span class="grid size-12 place-items-center rounded-xl border border-line bg-canvas text-base font-bold text-muted transition group-hover:border-accent/30 group-hover:bg-surface group-hover:text-accent shadow-2xs">
                                {{ $prefix }}
                            </span>
                            <div>
                                <span class="block text-base font-semibold text-ink group-hover:text-accent transition">{{ $label }}</span>
                                <span class="block text-xs text-muted">Antrean prefix {{ $prefix }}</span>
                            </div>
                        </div>
                        <div class="grid size-8 place-items-center rounded-full bg-canvas text-muted transition group-hover:bg-accent group-hover:text-white">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </div>
                    </button>
                @endforeach
            </fieldset>
        </form>

        <footer class="mt-10 border-t border-line pt-6 text-center text-xs text-muted">
            Butuh bantuan? Silakan tanyakan kepada staf toko kami.
        </footer>
    </div>
@endsection
