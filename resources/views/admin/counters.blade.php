@extends('layouts.admin')

@section('title', 'Loket — antrianQ')

@section('admin-content')
    <main class="w-full px-6 py-8 lg:px-10 lg:py-10">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight lg:text-3xl">Manajemen Loket</h1>
            <p class="mt-1.5 text-base text-muted">Atur loket layanan dan tentukan jenis antrean yang diproses masing-masing loket.</p>
        </div>

        {{-- Form Tambah Loket --}}
        <form method="POST" action="{{ route('admin.counters.store') }}" class="mt-7 flex flex-wrap items-end gap-4 rounded-2xl border border-line bg-surface p-6 shadow-2xs">
            @csrf
            <label class="min-w-56 flex-1">
                <span class="text-sm font-semibold text-muted">Nama Loket</span>
                <input
                    type="text"
                    name="name"
                    required
                    maxlength="50"
                    placeholder="Contoh: Loket 3"
                    class="mt-2 w-full rounded-xl border border-line bg-canvas/40 px-4 py-3 text-sm outline-none transition placeholder:text-muted-light focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15"
                >
            </label>
            <label class="min-w-52">
                <span class="text-sm font-semibold text-muted">Jenis Layanan</span>
                <select name="prefix" class="mt-2 w-full rounded-xl border border-line bg-canvas/40 px-4 py-3 text-sm outline-none transition focus:border-accent focus:bg-surface focus:ring-3 focus:ring-accent/15">
                    @foreach ($categories as $prefix => $label)
                        <option value="{{ $prefix }}">{{ $label }} ({{ $prefix }})</option>
                    @endforeach
                </select>
            </label>
            <button
                type="submit"
                class="rounded-xl bg-accent px-6 py-3 text-sm font-medium text-white shadow-xs transition hover:bg-accent-hover active:scale-[0.98]"
            >
                Tambah Loket
            </button>
        </form>
        @error('name')
            <p class="mt-2 text-sm font-medium text-skip">{{ $message }}</p>
        @enderror

        {{-- Tabel Loket --}}
        <div class="mt-6 overflow-hidden rounded-2xl border border-line bg-surface shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-line bg-canvas/50 text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="px-6 py-4">Nama Loket</th>
                            <th class="px-6 py-4">Kategori Layanan</th>
                            <th class="px-6 py-4">Staf Bertugas</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($counters as $counter)
                            <tr class="transition hover:bg-canvas/60">
                                <td class="px-6 py-5 text-base font-semibold text-ink">{{ $counter->name }}</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center gap-2 rounded-lg border border-line bg-canvas px-3 py-1.5 text-sm font-medium text-ink-light">
                                        <span class="font-semibold text-accent">{{ $counter->prefix }}</span>
                                        <span>{{ config('antrianq.categories.'.$counter->prefix) }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-5 tabular-nums text-sm text-muted">{{ $counter->users_count }} operator</td>
                                <td class="px-6 py-5">
                                    <span @class([
                                        'inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium',
                                        'bg-done-soft text-done' => $counter->is_active,
                                        'bg-canvas text-muted border border-line' => ! $counter->is_active,
                                    ])>
                                        <span @class([
                                            'size-2 rounded-full',
                                            'bg-done' => $counter->is_active,
                                            'bg-muted' => ! $counter->is_active,
                                        ])></span>
                                        {{ $counter->is_active ? 'Buka' : 'Tutup' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end items-center gap-2.5">
                                        <form method="POST" action="{{ route('admin.counters.toggle', $counter) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="rounded-lg border border-line px-4 py-2 text-sm font-medium text-ink-light transition hover:border-line-subtle hover:bg-canvas active:scale-[0.98]"
                                            >
                                                {{ $counter->is_active ? 'Tutup' : 'Buka' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.counters.destroy', $counter) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                onclick="confirmAction(this, { title: 'Hapus {{ addslashes($counter->name) }}?', text: 'Tindakan ini akan menghapus loket dan tidak dapat dibatalkan.', confirmButtonText: 'Ya, Hapus' })"
                                                class="rounded-lg border border-skip/30 bg-skip/5 px-4 py-2 text-sm font-medium text-skip transition hover:bg-skip/10 active:scale-[0.98]"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-base text-muted">Belum ada loket yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
