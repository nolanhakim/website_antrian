@extends('layouts.admin')

@section('title', 'Dashboard — antrianQ')

@section('admin-content')
    @php
        // Status "dipanggil" tidak dikirim controller; selisih inilah sisanya.
        $calledCount = max($total - $completedCount - $skippedCount - $waitingCount, 0);
        $pct = fn (int $n): int => $total > 0 ? (int) round($n / $total * 100) : 0;
        $peakMax = max($peakHours->max(), 1);
        $peakHour = $peakHours->filter()->sortDesc()->keys()->first();

        $segments = [
            ['label' => 'Layanan selesai', 'count' => $completedCount, 'color' => 'var(--color-done)', 'dot' => 'bg-done'],
            ['label' => 'Sedang dipanggil', 'count' => $calledCount, 'color' => 'var(--color-processing)', 'dot' => 'bg-processing'],
            ['label' => 'Nomor dilewati', 'count' => $skippedCount, 'color' => 'var(--color-skip)', 'dot' => 'bg-skip'],
            ['label' => 'Masih menunggu', 'count' => $waitingCount, 'color' => 'var(--color-muted-light)', 'dot' => 'bg-muted-light'],
        ];

        $stop = 0.0;
        $stops = [];
        foreach ($segments as $segment) {
            $end = $total > 0 ? $stop + $segment['count'] / $total * 100 : 0;
            if ($segment['count'] > 0) {
                $stops[] = $segment['color'].' '.round($stop, 2).'% '.round($end, 2).'%';
            }
            $stop = $end;
        }
        $donut = 'conic-gradient('.($stops ? implode(', ', $stops) : 'var(--color-line-subtle) 0 100%').')';
    @endphp

    <main class="w-full px-6 py-8 lg:px-10 lg:py-10">

        {{-- Header --}}
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-accent">Ringkasan hari ini</p>
                <h1 class="mt-1.5 text-2xl font-semibold tracking-tight lg:text-3xl">Dashboard pemilik</h1>
                <p class="mt-1.5 text-sm text-muted">{{ now()->locale('id')->translatedFormat('l, j F Y') }} · diperbarui {{ now()->format('H:i') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('admin.export') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-4 py-2.5 text-sm font-medium text-ink shadow-2xs transition hover:border-accent hover:text-accent active:scale-[0.98]"
                >
                    <svg class="size-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export CSV</span>
                </a>
                <form method="POST" action="{{ route('admin.queues.reset') }}">
                    @csrf
                    <button
                        type="button"
                        onclick="confirmAction(this, {
                            title: 'Reset Antrean Hari Ini?',
                            text: 'Semua antrean hari ini akan dihapus dan urutan nomor akan dimulai dari 1 kembali. Tindakan ini tidak dapat dibatalkan.',
                            confirmButtonText: 'Ya, Reset Antrean',
                            confirmButtonColor: '#dc2626'
                        })"
                        class="inline-flex items-center gap-2 rounded-xl border border-skip/25 bg-skip/5 px-4 py-2.5 text-sm font-medium text-skip shadow-2xs transition hover:bg-skip/10 active:scale-[0.98]"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>Reset Antrean</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- KPI --}}
        <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            {{-- Total pengunjung: kartu utama --}}
            <div class="relative overflow-hidden rounded-2xl border border-accent/20 bg-accent-soft/40 p-6 shadow-2xs">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-sm font-medium text-accent">Total Pengunjung</span>
                    <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-surface text-accent shadow-2xs">
                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    </span>
                </div>
                <p class="mt-5 text-4xl font-semibold tracking-tight tabular-nums text-ink lg:text-5xl">{{ $total }}</p>
                <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium text-accent">
                    <span class="size-1.5 rounded-full bg-accent"></span>
                    {{ $waitingCount }} antrean menunggu saat ini
                </p>
            </div>

            <div class="rounded-2xl border border-line bg-surface p-6 shadow-2xs">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-sm font-medium text-muted">Rata-rata Waktu Tunggu</span>
                    <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-canvas text-muted">
                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </span>
                </div>
                <p class="mt-5 text-4xl font-semibold tracking-tight tabular-nums text-ink lg:text-5xl">
                    {{ $avgWait }}<span class="ml-1 text-base font-normal text-muted">menit</span>
                </p>
                <p class="mt-2 text-xs text-muted">Tiket terbit hingga dipanggil</p>
            </div>

            <div class="rounded-2xl border border-line bg-surface p-6 shadow-2xs">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-sm font-medium text-muted">Durasi Layanan</span>
                    <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-canvas text-muted">
                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </span>
                </div>
                <p class="mt-5 text-4xl font-semibold tracking-tight tabular-nums text-ink lg:text-5xl">
                    {{ $avgService }}<span class="ml-1 text-base font-normal text-muted">menit</span>
                </p>
                <p class="mt-2 text-xs text-muted">{{ $completedCount }} tiket terselesaikan</p>
            </div>

            <div class="rounded-2xl border border-line bg-surface p-6 shadow-2xs">
                <div class="flex items-start justify-between gap-3">
                    <span class="text-sm font-medium text-muted">Tingkat Dilewati</span>
                    <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-canvas text-muted">
                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </span>
                </div>
                <p class="mt-5 flex items-baseline gap-1.5 text-4xl font-semibold tracking-tight tabular-nums text-ink lg:text-5xl">
                    {{ $skippedCount }}<span class="text-base font-normal text-muted">({{ $pct($skippedCount) }}%)</span>
                </p>
                <p class="mt-2 text-xs text-muted">Dilewati karena tidak hadir</p>
            </div>
        </div>

        {{-- Chart + Donut --}}
        <div class="mt-5 grid gap-5 xl:grid-cols-[1.75fr_1fr]">
            <section class="rounded-2xl border border-line bg-surface p-6 shadow-2xs">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold tracking-tight">Kepadatan jam sibuk</h2>
                        <p class="mt-1 text-xs text-muted">Distribusi pengunjung per jam, 08:00 – 21:00</p>
                    </div>
                    @if ($peakHour)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-accent/20 bg-accent-soft/60 px-3 py-1 text-xs font-medium text-accent">
                            Puncak {{ $peakHour }} · {{ $peakMax }} orang
                        </span>
                    @endif
                </div>

                {{-- Gridlines + bar chart --}}
                <div class="relative mt-7 h-56">
                    <div class="absolute inset-0 flex flex-col justify-between" aria-hidden="true">
                        @for ($i = 0; $i < 4; $i++)
                            <div class="border-t border-line-subtle"></div>
                        @endfor
                    </div>

                    <div class="relative flex h-full items-end gap-1.5 border-b border-line pb-2 lg:gap-2">
                        @foreach ($peakHours as $hour => $count)
                            @php $isPeak = $count === $peakHours->max() && $count > 0; @endphp
                            <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-2">
                                <div class="pointer-events-none absolute -top-9 z-10 hidden whitespace-nowrap rounded-lg bg-ink px-2.5 py-1.5 text-[11px] font-medium text-white shadow-sm group-hover:block">
                                    {{ $hour }} · {{ $count }} orang
                                </div>
                                <div
                                    class="w-full rounded-t-md transition-colors duration-200 {{ $isPeak ? 'bg-accent' : 'bg-line group-hover:bg-muted-light' }}"
                                    style="height: {{ round($count / $peakMax * 100) }}%; min-height: {{ $count > 0 ? '5px' : '0' }};"
                                ></div>
                                <span class="text-[10px] tabular-nums text-muted-light group-hover:text-ink">{{ substr($hour, 0, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="flex flex-col rounded-2xl border border-line bg-surface p-6 shadow-2xs">
                <div>
                    <h2 class="text-base font-semibold tracking-tight">Status antrean</h2>
                    <p class="mt-1 text-xs text-muted">Komposisi seluruh tiket hari ini</p>
                </div>

                <div class="mt-6 flex items-center justify-center">
                    <div class="relative grid size-44 place-items-center rounded-full" style="background: {{ $donut }}">
                        <div class="grid size-32 place-items-center rounded-full bg-surface text-center">
                            <div>
                                <p class="text-3xl font-semibold tabular-nums leading-none text-ink">{{ $total }}</p>
                                <p class="mt-1 text-[11px] font-medium uppercase tracking-wider text-muted">tiket</p>
                            </div>
                        </div>
                    </div>
                </div>

                <dl class="mt-6 space-y-3 text-sm">
                    @foreach ($segments as $segment)
                        <div class="flex items-center justify-between gap-3">
                            <dt class="flex items-center gap-2.5 text-muted">
                                <span class="size-2 rounded-full {{ $segment['dot'] }}"></span>
                                <span>{{ $segment['label'] }}</span>
                            </dt>
                            <dd class="flex items-baseline gap-2">
                                <span class="font-semibold tabular-nums text-ink">{{ $segment['count'] }}</span>
                                <span class="w-9 text-right text-xs tabular-nums text-muted-light">{{ $pct($segment['count']) }}%</span>
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-auto border-t border-line pt-4">
                    <a href="{{ route('display') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-accent transition hover:text-accent-hover">
                        <span>Buka layar ruang tunggu</span>
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                    </a>
                </div>
            </section>
        </div>

        {{-- Antrean terakhir --}}
        <section class="mt-5 overflow-hidden rounded-2xl border border-line bg-surface shadow-2xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-6 py-5">
                <div>
                    <h2 class="text-base font-semibold tracking-tight">Antrean terakhir</h2>
                    <p class="mt-1 text-xs text-muted">10 aktivitas antrean terbaru</p>
                </div>
                <span class="rounded-full border border-line bg-canvas px-3 py-1 text-xs tabular-nums text-muted">{{ $queues->count() }} dari {{ $total }} tiket</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-line bg-canvas/50 text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="px-6 py-4">Nomor</th>
                            <th class="px-6 py-4">Layanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Loket</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Jam tiket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($queues as $queue)
                            <tr class="transition hover:bg-canvas/60">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-lg border border-line bg-canvas px-2.5 py-1 text-sm font-semibold tabular-nums text-ink">{{ $queue->queue_number }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-muted">{{ config('antrianq.categories.'.$queue->prefix, $queue->prefix) }}</td>
                                <td class="px-6 py-4 text-sm text-ink-light">{{ $queue->customer_name ?: '—' }}</td>
                                <td class="px-6 py-4 text-sm text-muted">{{ $queue->counter?->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span @class([
                                        'inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium',
                                        'bg-canvas text-muted border border-line' => $queue->status === \App\Models\Queue::STATUS_WAITING,
                                        'bg-processing/10 text-processing' => $queue->status === \App\Models\Queue::STATUS_CALLED,
                                        'bg-done-soft text-done' => $queue->status === \App\Models\Queue::STATUS_COMPLETED,
                                        'bg-skip-soft text-skip' => $queue->status === \App\Models\Queue::STATUS_SKIPPED,
                                    ])>
                                        <span @class([
                                            'size-1.5 rounded-full',
                                            'bg-muted' => $queue->status === \App\Models\Queue::STATUS_WAITING,
                                            'bg-processing animate-pulse' => $queue->status === \App\Models\Queue::STATUS_CALLED,
                                            'bg-done' => $queue->status === \App\Models\Queue::STATUS_COMPLETED,
                                            'bg-skip' => $queue->status === \App\Models\Queue::STATUS_SKIPPED,
                                        ])></span>
                                        {{ ucfirst($queue->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm tabular-nums text-muted">{{ $queue->created_at->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-sm text-muted">Belum ada antrean yang terdaftar hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
