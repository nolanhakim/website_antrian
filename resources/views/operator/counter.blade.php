@extends('layouts.app')

@section('title', 'Panel '.$counter->name.' — antrianQ')

@section('content')
    <div
        class="mx-auto flex min-h-dvh w-full max-w-md flex-col justify-between px-5 py-6"
        x-data="operatorPanel(@js([
            'prefix'  => $counter->prefix,
            'waiting'  => $waiting->values(),
            'done'     => $done,
            'skipped'  => $skipped,
            'stateUrl' => route('operator.state'),
        ]))"
    >
        {{-- ===== Header ===== --}}
        <header class="flex items-center justify-between border-b border-line pb-4">
            <div class="flex items-center gap-3">
                <span class="grid size-9 place-items-center rounded-xl bg-accent text-xs font-bold text-white shadow-2xs">
                    {{ $counter->prefix }}
                </span>
                <div>
                    <h1 class="text-base font-semibold tracking-tight text-ink">{{ $counter->name }}</h1>
                    <p class="text-xs text-muted">{{ config('antrianq.categories.'.$counter->prefix) }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs">
                {{-- Live indicator --}}
                <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-canvas px-2.5 py-1 text-[11px] font-medium">
                    <span class="size-1.5 rounded-full" :class="connected ? 'bg-done' : 'bg-skip animate-pulse'"></span>
                    <span x-text="connected ? 'Live' : 'Polling'"></span>
                </span>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-line bg-surface px-2.5 py-1 font-medium text-muted transition hover:border-line-subtle hover:text-ink">
                        Admin
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-line bg-surface px-2.5 py-1 font-medium text-muted transition hover:border-skip/30 hover:bg-skip/5 hover:text-skip">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        {{-- ===== Nomor aktif Card ===== --}}
        <div class="my-auto py-6">
            <div class="rounded-2xl border border-line bg-surface p-8 text-center shadow-2xs">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-canvas px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-muted border border-line">
                    <span class="size-1.5 rounded-full" :class="currentNumber ? 'bg-accent animate-pulse' : 'bg-muted-light'"></span>
                    <span x-text="currentNumber ? 'Sedang Dilayani' : 'Menunggu Panggilan'"></span>
                </span>

                <p class="mt-6 text-[clamp(4.5rem,22vw,7rem)] leading-none font-semibold tracking-tight tabular-nums text-ink"
                   x-text="currentNumber || '—'"></p>

                @if ($current?->customer_name)
                    <div class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-accent-soft px-3.5 py-1 text-xs font-medium text-accent">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>{{ $current->customer_name }}</span>
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-3 divide-x divide-line border-t border-line pt-4 text-center">
                    <div>
                        <span class="block text-lg font-semibold tabular-nums text-ink" x-text="waitingCount"></span>
                        <span class="text-[11px] text-muted">menunggu</span>
                    </div>
                    <div>
                        <span class="block text-lg font-semibold tabular-nums text-done" x-text="doneCount"></span>
                        <span class="text-[11px] text-muted">selesai</span>
                    </div>
                    <div>
                        <span class="block text-lg font-semibold tabular-nums text-skip" x-text="skippedCount"></span>
                        <span class="text-[11px] text-muted">dilewati</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Kontrol Aksi ===== --}}
        <div class="space-y-3">
            {{-- Tombol Utama: Panggil Berikutnya --}}
            <form method="POST" action="{{ route('operator.next') }}">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2.5 rounded-xl bg-accent px-5 py-4 text-base font-semibold text-white shadow-xs transition hover:bg-accent-hover active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                    :disabled="waitingCount === 0"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                    </svg>
                    <span>Panggil Berikutnya</span>
                </button>
            </form>

            {{-- Tiga Tombol Sekunder --}}
            <div class="grid grid-cols-3 gap-2.5">
                {{-- Panggil Ulang --}}
                <form method="POST" action="{{ route('operator.recall') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full flex-col items-center justify-center gap-1 rounded-xl border border-line bg-surface py-3 text-xs font-medium text-ink shadow-2xs transition hover:border-line-subtle hover:bg-canvas active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                        :disabled="! currentNumber"
                    >
                        <svg class="size-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>Panggil Ulang</span>
                    </button>
                </form>

                {{-- Lewati --}}
                <form method="POST" action="{{ route('operator.skip') }}">
                    @csrf
                    <button
                        type="button"
                        onclick="confirmAction(this, { title: 'Lewati antrean?', text: 'Nomor ini akan ditandai terlewat dan dapat dipanggil lagi nanti.', confirmButtonText: 'Ya, Lewati' })"
                        class="flex w-full flex-col items-center justify-center gap-1 rounded-xl border border-skip/30 bg-skip-soft/30 py-3 text-xs font-medium text-skip shadow-2xs transition hover:bg-skip-soft active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                        :disabled="! currentNumber"
                    >
                        <svg class="size-4 text-skip" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Lewati</span>
                    </button>
                </form>

                {{-- Selesai --}}
                <form method="POST" action="{{ route('operator.finish') }}">
                    @csrf
                    <button
                        type="button"
                        onclick="confirmAction(this, { title: 'Selesaikan antrean?', text: 'Layanan selesai untuk nomor antrean ini.', icon: 'question', confirmButtonColor: '#059669', confirmButtonText: 'Ya, Selesai' })"
                        class="flex w-full flex-col items-center justify-center gap-1 rounded-xl border border-done/30 bg-done-soft/30 py-3 text-xs font-medium text-done shadow-2xs transition hover:bg-done-soft active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                        :disabled="! currentNumber"
                    >
                        <svg class="size-4 text-done" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Selesai</span>
                    </button>
                </form>
            </div>

            {{-- Antrean Berikutnya Preview --}}
            <p x-show="waitingList.length" x-cloak class="pt-2 text-center text-xs text-muted">
                Urutan berikutnya: <strong class="font-semibold tabular-nums text-ink" x-text="waitingList.slice(0, 4).join(', ')"></strong>
            </p>

            {{-- Daftar Terlewat --}}
            @if ($skippedToday->isNotEmpty())
                <div class="rounded-2xl border border-line bg-surface p-4 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-muted">Dilewati Hari Ini</span>
                        <span class="text-[11px] text-muted-light">Klik untuk panggil ulang</span>
                    </div>
                    <div class="mt-2.5 flex flex-wrap gap-2">
                        @foreach ($skippedToday as $queue)
                            <form method="POST" action="{{ route('operator.recallSkipped', $queue) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1 rounded-lg border border-skip/30 bg-skip-soft/30 px-3 py-1.5 text-xs font-semibold tabular-nums text-skip transition hover:bg-skip-soft active:scale-[0.98]"
                                >
                                    <span>{{ $queue->queue_number }}</span>
                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function operatorPanel(config) {
            return {
                currentNumber: @json($current?->queue_number),
                waitingList: config.waiting,
                waitingCount: config.waiting.length,
                doneCount: config.done,
                skippedCount: config.skipped,
                connected: false,
                _pollTimer: null,

                init() {
                    const channel = window.Echo.channel('queue');

                    // Antrean baru dibuat dari kiosk
                    channel.listen('.queue.created', (payload) => {
                        if (payload.prefix === config.prefix) {
                            this.refresh();
                            this.notify(payload.queue_number);
                        }
                    });

                    // Antrean dipanggil (oleh operator manapun)
                    channel.listen('.queue.called', () => {
                        this.refresh();
                    });

                    // Antrean direset oleh admin
                    channel.listen('.queue.reset', () => {
                        this.refresh();
                    });

                    // Connection status
                    const pusher = window.Echo.connector.pusher;
                    pusher.connection.bind('connected', () => {
                        this.connected = true;
                        this.stopPolling();
                    });
                    pusher.connection.bind('disconnected', () => {
                        this.connected = false;
                        this.startPolling();
                    });
                    pusher.connection.bind('unavailable', () => {
                        this.connected = false;
                        this.startPolling();
                    });

                    if (pusher.connection.state === 'connected') {
                        this.connected = true;
                    } else {
                        this.startPolling();
                    }
                },

                async refresh() {
                    try {
                        const res = await fetch(config.stateUrl);
                        if (!res.ok) return;
                        const data = await res.json();
                        this.waitingList = data.waiting;
                        this.waitingCount = data.waiting.length;
                        this.doneCount = data.done;
                        this.skippedCount = data.skipped;
                    } catch (_) {}
                },

                notify(number) {
                    this.playBeep();
                    if (window.Swal) {
                        window.Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Antrean baru: ' + number,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                },

                playBeep() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        if (ctx.state === 'suspended') ctx.resume();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                        osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.12);
                        gain.gain.setValueAtTime(0.2, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    } catch (_) {}
                },

                startPolling() {
                    if (this._pollTimer) return;
                    this._pollTimer = setInterval(() => this.refresh(), 10000);
                },
                stopPolling() {
                    if (this._pollTimer) {
                        clearInterval(this._pollTimer);
                        this._pollTimer = null;
                    }
                },
            };
        }
    </script>
@endsection
