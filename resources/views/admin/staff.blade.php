@extends('layouts.admin')

@section('title', 'Staf — antrianQ')

@section('admin-content')
    @php
        $totalStaff = $staff->count();
        $adminCount = $staff->where('role', 'admin')->count();
        $operatorCount = $staff->where('role', 'operator')->count();
    @endphp

    <main class="w-full px-6 py-8 lg:px-10 lg:py-10">

        {{-- Header --}}
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-accent">Akses &amp; tim</p>
                <h1 class="mt-1.5 text-2xl font-semibold tracking-tight lg:text-3xl">Manajemen Staf</h1>
                <p class="mt-1.5 text-sm text-muted">Kelola akun administrator dan operator loket pemanggil antrean.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <span class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-4 py-2.5 text-sm shadow-2xs">
                    <span class="size-2 rounded-full bg-muted-light"></span>
                    <span class="text-muted">Total</span>
                    <strong class="font-semibold tabular-nums text-ink">{{ $totalStaff }}</strong>
                </span>
                <span class="inline-flex items-center gap-2 rounded-xl border border-accent/20 bg-accent-soft/50 px-4 py-2.5 text-sm shadow-2xs">
                    <span class="size-2 rounded-full bg-accent"></span>
                    <span class="text-accent">Admin</span>
                    <strong class="font-semibold tabular-nums text-ink">{{ $adminCount }}</strong>
                </span>
                <span class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-4 py-2.5 text-sm shadow-2xs">
                    <span class="size-2 rounded-full bg-processing"></span>
                    <span class="text-muted">Operator</span>
                    <strong class="font-semibold tabular-nums text-ink">{{ $operatorCount }}</strong>
                </span>
            </div>
        </div>

        {{-- Form tambah akun --}}
        <section class="mt-7 rounded-2xl border border-line bg-surface p-6 shadow-2xs">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="text-base font-semibold tracking-tight">Tambah akun baru</h2>
                <p class="text-xs text-muted">Operator hanya bisa memanggil antrean dari loket yang ditugaskan.</p>
            </div>

            <form method="POST" action="{{ route('admin.staff.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <label>
                    <span class="text-sm font-semibold text-muted">Nama lengkap</span>
                    <input
                        type="text"
                        name="name"
                        required
                        maxlength="50"
                        value="{{ old('name') }}"
                        placeholder="Nama staf"
                        class="mt-2 w-full rounded-xl border border-line bg-canvas/40 px-4 py-3 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15"
                    >
                </label>
                <label>
                    <span class="text-sm font-semibold text-muted">Email login</span>
                    <input
                        type="email"
                        name="email"
                        required
                        value="{{ old('email') }}"
                        placeholder="staf@antrianq.test"
                        class="mt-2 w-full rounded-xl border border-line bg-canvas/40 px-4 py-3 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15"
                    >
                </label>
                <label>
                    <span class="text-sm font-semibold text-muted">Password</span>
                    <input
                        type="password"
                        name="password"
                        required
                        minlength="8"
                        placeholder="Min. 8 karakter"
                        class="mt-2 w-full rounded-xl border border-line bg-canvas/40 px-4 py-3 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15"
                    >
                </label>
                <label>
                    <span class="text-sm font-semibold text-muted">Peran akun</span>
                    <select name="role" class="mt-2 w-full rounded-xl border border-line bg-canvas/40 px-4 py-3 text-sm outline-none transition focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15">
                        <option value="operator">Operator loket</option>
                        <option value="admin">Administrator</option>
                    </select>
                </label>
                <label>
                    <span class="text-sm font-semibold text-muted">Loket ditugaskan</span>
                    <select name="counter_id" class="mt-2 w-full rounded-xl border border-line bg-canvas/40 px-4 py-3 text-sm outline-none transition focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15">
                        <option value="">— Belum ditugaskan —</option>
                        @foreach ($counters as $counter)
                            <option value="{{ $counter->id }}">{{ $counter->name }} ({{ config('antrianq.categories.'.$counter->prefix) }})</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex items-end">
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-accent px-5 py-3 text-sm font-medium text-white shadow-xs transition hover:bg-accent-hover active:scale-[0.98]"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>Simpan akun</span>
                    </button>
                </div>
            </form>
        </section>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-skip/20 bg-skip-soft/50 p-4">
                <p class="text-sm font-semibold text-skip">Periksa kembali data akun</p>
                <ul class="mt-1.5 space-y-1 text-sm text-skip">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Daftar akun --}}
        <section class="mt-7">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-semibold tracking-tight">Daftar akun terdaftar</h2>
                <span class="rounded-full border border-line bg-surface px-3 py-1 text-xs tabular-nums text-muted">{{ $totalStaff }} akun</span>
            </div>

            @if ($totalStaff === 0)
                <div class="mt-4 rounded-2xl border border-dashed border-line bg-surface px-6 py-16 text-center">
                    <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-canvas text-muted-light">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" /></svg>
                    </span>
                    <p class="mt-3 text-sm font-medium text-ink">Belum ada akun staf</p>
                    <p class="mt-1 text-sm text-muted">Tambahkan akun pertama lewat formulir di atas.</p>
                </div>
            @else
                <div class="mt-4 grid gap-4 xl:grid-cols-2">
                    @foreach ($staff as $user)
                        <details class="group h-fit rounded-2xl border border-line bg-surface shadow-2xs transition-all open:border-accent/30 open:ring-1 open:ring-accent/15">
                            <summary class="flex cursor-pointer select-none items-center gap-4 px-5 py-4">
                                <span @class([
                                    'grid size-11 shrink-0 place-items-center rounded-xl text-sm font-semibold shadow-2xs',
                                    'bg-accent-soft text-accent' => $user->role === 'admin',
                                    'bg-canvas text-ink-light border border-line' => $user->role !== 'admin',
                                ])>
                                    {{ mb_substr($user->name, 0, 1) }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="truncate text-sm font-semibold text-ink">{{ $user->name }}</span>
                                        @if ($user->is(auth()->user()))
                                            <span class="rounded-md bg-canvas px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-muted">Anda</span>
                                        @endif
                                    </div>
                                    <span class="mt-0.5 block truncate text-xs text-muted">{{ $user->email }}</span>
                                </div>

                                <div class="flex shrink-0 items-center gap-2.5">
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-accent-soft text-accent' => $user->role === 'admin',
                                        'bg-canvas text-muted border border-line' => $user->role !== 'admin',
                                    ])>
                                        <span @class([
                                            'size-1.5 rounded-full',
                                            'bg-accent' => $user->role === 'admin',
                                            'bg-muted-light' => $user->role !== 'admin',
                                        ])></span>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <span class="hidden items-center gap-1 rounded-lg border border-line bg-canvas px-2 py-1 text-xs text-muted sm:inline-flex">
                                        <svg class="size-3.5 text-muted-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
                                        <span>{{ $user->counter?->name ?? 'Tanpa loket' }}</span>
                                    </span>
                                    <svg class="size-4 shrink-0 text-muted transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </summary>

                            <div class="border-t border-line bg-canvas/30 px-5 py-5">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-muted-light">Ubah data akun</p>

                                <form method="POST" action="{{ route('admin.staff.update', $user) }}" class="mt-3 grid gap-4 sm:grid-cols-2">
                                    @csrf
                                    @method('PATCH')
                                    <label>
                                        <span class="text-sm font-semibold text-muted">Nama</span>
                                        <input
                                            type="text"
                                            name="name"
                                            required
                                            maxlength="50"
                                            value="{{ $user->name }}"
                                            class="mt-2 w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm outline-none transition focus:border-accent focus:ring-3 focus:ring-accent/15"
                                        >
                                    </label>
                                    <label>
                                        <span class="text-sm font-semibold text-muted">Email</span>
                                        <input
                                            type="email"
                                            name="email"
                                            required
                                            value="{{ $user->email }}"
                                            class="mt-2 w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm outline-none transition focus:border-accent focus:ring-3 focus:ring-accent/15"
                                        >
                                    </label>
                                    <label>
                                        <span class="text-sm font-semibold text-muted">Password baru</span>
                                        <input
                                            type="password"
                                            name="password"
                                            minlength="8"
                                            placeholder="Kosongkan jika tetap"
                                            class="mt-2 w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:ring-3 focus:ring-accent/15"
                                        >
                                    </label>
                                    <label>
                                        <span class="text-sm font-semibold text-muted">Peran</span>
                                        <select name="role" class="mt-2 w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm outline-none transition focus:border-accent focus:ring-3 focus:ring-accent/15">
                                            <option value="operator" @selected($user->role === 'operator')>Operator</option>
                                            <option value="admin" @selected($user->role === 'admin')>Administrator</option>
                                        </select>
                                    </label>
                                    <label class="sm:col-span-2">
                                        <span class="text-sm font-semibold text-muted">Loket</span>
                                        <select name="counter_id" class="mt-2 w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm outline-none transition focus:border-accent focus:ring-3 focus:ring-accent/15">
                                            <option value="">— Belum ditugaskan —</option>
                                            @foreach ($counters as $counter)
                                                <option value="{{ $counter->id }}" @selected($user->counter_id === $counter->id)>
                                                    {{ $counter->name }} ({{ config('antrianq.categories.'.$counter->prefix) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <div class="sm:col-span-2">
                                        <button
                                            type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-xl bg-accent px-5 py-3 text-sm font-medium text-white shadow-xs transition hover:bg-accent-hover active:scale-[0.98] sm:w-auto"
                                        >
                                            Perbarui akun
                                        </button>
                                    </div>
                                </form>

                                @unless ($user->is(auth()->user()))
                                    <div class="mt-5 flex items-center justify-between gap-3 border-t border-line pt-4">
                                        <p class="text-xs text-muted">Menghapus akun ini akan mencabut akses login staf.</p>
                                        <form method="POST" action="{{ route('admin.staff.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                onclick="confirmAction(this, { title: 'Hapus akun {{ addslashes($user->name) }}?', text: 'Tindakan ini akan menghapus akun pengguna ini dan tidak dapat dibatalkan.', confirmButtonText: 'Ya, Hapus' })"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-skip/25 bg-skip/5 px-3 py-2 text-sm font-medium text-skip transition hover:bg-skip/10 active:scale-[0.98]"
                                            >
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                <span>Hapus akun</span>
                                            </button>
                                        </form>
                                    </div>
                                @endunless
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
@endsection
