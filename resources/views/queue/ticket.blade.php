@extends('layouts.app')

@section('title', 'Tiket '.$queue->queue_number.' — antrianQ')

@section('content')
    <div
        class="print-ticket mx-auto flex min-h-dvh w-full max-w-md flex-col justify-center gap-6 px-6 py-10"
        x-data="ticketTracker(@js([
            'status' => $queue->status,
            'counter' => $queue->counter?->name,
            'ahead' => $ahead,
            'minutes' => $minutes,
            'statusUrl' => route('ticket.status', $queue),
        ]))"
    >
        <div class="rounded-2xl border border-line bg-surface p-8 text-center shadow-2xs">
            <div class="flex items-center justify-center gap-2">
                <span class="grid size-6 place-items-center rounded-lg bg-accent text-[11px] font-bold text-white">Q</span>
                <span class="text-xs font-semibold tracking-wider uppercase text-muted">{{ $service }}</span>
            </div>

            <p class="mt-6 text-[5.5rem] leading-none font-semibold tracking-tighter tabular-nums text-ink">
                {{ $queue->queue_number }}
            </p>

            @if ($queue->customer_name)
                <p class="mt-2 text-sm font-medium text-ink-light">{{ $queue->customer_name }}</p>
            @endif

            <div class="mt-6 flex justify-center">
                <template x-if="status === 'called'">
                    <span class="inline-flex items-center gap-2 rounded-full bg-accent-soft px-4 py-1.5 text-sm font-semibold text-accent animate-pulse">
                        <span class="size-2 rounded-full bg-accent"></span>
                        <span x-text="'Silakan menuju ' + (counter || 'Loket')">Silakan menuju {{ $queue->counter?->name }}</span>
                    </span>
                </template>
                <template x-if="status === 'completed'">
                    <span class="inline-flex items-center gap-2 rounded-full bg-done-soft px-4 py-1.5 text-sm font-semibold text-done">
                        <span class="size-2 rounded-full bg-done"></span>
                        Layanan selesai
                    </span>
                </template>
                <template x-if="status === 'skipped'">
                    <span class="inline-flex items-center gap-2 rounded-full bg-skip-soft px-4 py-1.5 text-sm font-semibold text-skip">
                        <span class="size-2 rounded-full bg-skip"></span>
                        Nomor dilewati
                    </span>
                </template>
                <template x-if="status === 'waiting'">
                    <span class="inline-flex items-center gap-2 rounded-full bg-canvas px-4 py-1.5 text-sm font-medium text-muted border border-line">
                        <span class="size-2 rounded-full bg-muted-light"></span>
                        Menunggu dipanggil
                    </span>
                </template>
            </div>

            <dl class="mt-8 grid grid-cols-2 divide-x divide-line border-t border-line pt-6">
                <div>
                    <dd class="text-3xl font-semibold tabular-nums text-ink" x-text="ahead">{{ $ahead }}</dd>
                    <dt class="mt-1 text-xs text-muted">antrean di depan</dt>
                </div>
                <div>
                    <dd class="text-3xl font-semibold tabular-nums text-ink"><span x-text="minutes">{{ $minutes }}</span> <span class="text-sm font-normal text-muted">mnt</span></dd>
                    <dt class="mt-1 text-xs text-muted">estimasi waktu</dt>
                </div>
            </dl>
        </div>

        <p class="no-print text-center text-xs text-muted">
            Nomor Anda akan dipanggil melalui speaker & layar ruang tunggu.
        </p>

        <div class="no-print flex items-center justify-center gap-3">
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-4 py-2 text-xs font-semibold text-ink shadow-2xs transition hover:border-accent hover:text-accent active:scale-[0.98]"
            >
                <svg class="size-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.25 7.034V3.375" />
                </svg>
                <span>Cetak Tiket</span>
            </button>
            <a href="{{ route('kiosk') }}" class="rounded-xl px-4 py-2 text-xs font-semibold text-muted hover:text-ink transition">
                Kembali
            </a>
        </div>
    </div>

    <script>
        function ticketTracker(config) {
            return {
                status: config.status,
                counter: config.counter,
                ahead: config.ahead,
                minutes: config.minutes,

                init() {
                    if (window.Echo) {
                        const channel = window.Echo.channel('queue');
                        channel.listen('.queue.called', () => this.fetchStatus());
                        channel.listen('.queue.created', () => this.fetchStatus());
                    }

                    // Polling halus tiap 8 detik tanpa reload layar
                    const timer = setInterval(() => {
                        if (this.status !== 'waiting' && this.status !== 'called') {
                            clearInterval(timer);
                            return;
                        }
                        this.fetchStatus();
                    }, 8000);
                },

                async fetchStatus() {
                    try {
                        const res = await fetch(config.statusUrl);
                        if (!res.ok) return;
                        const data = await res.json();
                        this.status = data.status;
                        this.counter = data.counter;
                        this.ahead = data.ahead;
                        this.minutes = data.minutes;
                    } catch (_) {}
                }
            };
        }
    </script>
@endsection
