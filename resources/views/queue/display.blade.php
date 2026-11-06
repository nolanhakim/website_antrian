<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Display Antrean — antrianQ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-dvh overflow-hidden bg-surface font-sans text-ink antialiased">
    <div
        class="grid h-full grid-cols-1 lg:grid-cols-[1fr_21rem]"
        x-data="queueDisplay(@js([
            'current' => $current,
            'previous' => $previous,
            'waiting' => $waiting,
        ]))"
    >
        {{-- ===== Panggilan aktif ===== --}}
        <main class="flex flex-col justify-between px-10 py-9 lg:px-16">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="grid size-8 place-items-center rounded-md bg-accent text-sm font-bold text-white">Q</span>
                    <span class="text-sm font-semibold tracking-tight">{{ config('app.name') }}</span>
                </div>

                <button
                    type="button"
                    x-show="! soundOn"
                    @click="enableSound()"
                    class="inline-flex items-center gap-2 rounded-xl bg-accent px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-accent-hover active:scale-[0.98]"
                >
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                    </svg>
                    <span>Aktifkan Suara</span>
                </button>
                <div x-show="soundOn" x-cloak class="flex items-center gap-3 text-xs text-muted">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-canvas px-2.5 py-1 text-[11px] font-medium">
                        <span class="size-1.5 rounded-full" :class="connected ? 'bg-done' : 'bg-skip animate-pulse'"></span>
                        <span x-text="connected ? 'Live' : 'Polling'"></span>
                    </span>
                    <button type="button" @click="speak('Tes suara aktif.')" class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-medium text-accent hover:bg-accent-soft transition">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                        </svg>
                        <span>Uji Suara</span>
                    </button>
                </div>
            </div>

            <div aria-live="polite" class="py-8">
                <p class="text-sm font-semibold tracking-[0.25em] text-muted uppercase">Nomor dipanggil</p>

                <p class="mt-6 text-[clamp(7rem,21vw,19rem)] leading-[0.82] font-semibold tracking-tighter tabular-nums"
                   x-text="current?.number ?? '—'"></p>

                <div class="mt-8 flex items-baseline gap-3">
                    <template x-if="current?.counter">
                        <p class="text-[clamp(1.5rem,3.2vw,2.75rem)] font-medium text-muted">
                            silakan menuju
                            <span class="font-semibold text-accent" x-text="current.counter"></span>
                        </p>
                    </template>
                    <template x-if="! current?.counter">
                        <p class="text-[clamp(1.5rem,3.2vw,2.75rem)] font-medium text-muted/60">menunggu antrean</p>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <template x-for="(item, index) in previous" :key="index">
                    <div class="rounded-lg bg-canvas px-5 py-4">
                        <p class="text-xs font-medium tracking-wider text-muted uppercase">Sebelumnya</p>
                        <p class="mt-1 text-[clamp(1.4rem,2.6vw,2.1rem)] font-semibold tabular-nums" x-text="item.number"></p>
                        <p class="text-sm text-muted" x-text="item.counter"></p>
                    </div>
                </template>
            </div>
        </main>

        {{-- ===== Antrean berikutnya + promo ===== --}}
        <aside class="hidden flex-col gap-8 border-l border-line px-8 py-9 lg:flex">
            <div>
                <p class="text-xs font-semibold tracking-[0.15em] text-muted uppercase">Berikutnya</p>
                <ul class="mt-4 divide-y divide-line">
                    <template x-for="(number, index) in waiting" :key="index">
                        <li class="py-3.5 text-[clamp(1.3rem,2vw,1.75rem)] font-semibold tabular-nums text-ink/80" x-text="number"></li>
                    </template>
                </ul>
                <p x-show="waiting.length === 0" x-cloak class="mt-4 text-sm text-muted">Tidak ada antrean menunggu.</p>
            </div>

            <div class="mt-auto rounded-lg bg-accent-soft px-5 py-6">
                <p class="text-xs font-semibold tracking-[0.15em] text-accent uppercase">Promo</p>
                <p class="mt-2 text-lg leading-snug font-semibold text-ink">Potongan 20% untuk layanan kedua hari ini.</p>
                <p class="mt-2 text-sm text-ink/60">Tanyakan ke kasir sebelum membayar.</p>
            </div>
        </aside>

        {{-- Banner Mengambang Pasca Reboot / Suara Belum Aktif --}}
        <div
            x-show="! soundOn"
            x-cloak
            class="fixed inset-x-0 bottom-6 z-50 mx-auto max-w-lg px-4"
        >
            <div
                @click="enableSound()"
                class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-skip/30 bg-surface/95 p-4 shadow-xl backdrop-blur-md transition hover:scale-[1.01] active:scale-[0.99]"
            >
                <div class="flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-xl bg-skip/10 text-skip animate-pulse">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75 19.5 12m0 0 2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6 4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-ink">Suara Panggilan Belum Aktif</p>
                        <p class="text-[11px] text-muted">Klik di sini atau tekan sembarang tombol remote TV.</p>
                    </div>
                </div>
                <span class="rounded-xl bg-accent px-3 py-1.5 text-xs font-semibold text-white shadow-xs">
                    Aktifkan
                </span>
            </div>
        </div>
    </div>

    <script>
        function queueDisplay(initial) {
            return {
                current: initial.current,
                previous: initial.previous,
                waiting: initial.waiting,
                soundOn: false,
                connected: false,
                _pollTimer: null,
                _lastKey: initial.current?.key ?? null,
                init() {
                    const channel = window.Echo.channel('queue');

                    channel.listen('.queue.called', (payload) => {
                        this.applyState(payload, true);
                    });

                    // Antrean baru: update daftar tunggu
                    channel.listen('.queue.created', (payload) => {
                        if (payload.display) {
                            this.applyState(payload.display, false);
                        }
                    });

                    // Antrean direset admin: update layar
                    channel.listen('.queue.reset', (payload) => {
                        this.applyState(payload, false);
                    });

                    // Pusher connection status
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

                    // Deteksi audio context: jika browser mengizinkan autoplay tanpa gesture, langsung aktifkan
                    try {
                        const testCtx = new (window.AudioContext || window.webkitAudioContext)();
                        if (testCtx.state === 'running') {
                            this.soundOn = true;
                        }
                    } catch (_) {}

                    // Tangkap interaksi pertama (klik, sentuh, tombol keyboard, tombol remote TV)
                    const unlockEvents = ['click', 'keydown', 'touchstart', 'pointerdown'];
                    const unlock = () => {
                        if (! this.soundOn) this.enableSound();
                        unlockEvents.forEach(evt => document.removeEventListener(evt, unlock));
                    };
                    unlockEvents.forEach(evt => document.addEventListener(evt, unlock, { passive: true }));
                },
                applyState(state, force = false) {
                    const newKey = state.current?.key ?? null;
                    const changed = newKey && newKey !== this._lastKey;

                    this.current = state.current;
                    this.previous = state.previous;
                    this.waiting = state.waiting;
                    this._lastKey = newKey;

                    if (force || changed) this.speak();
                },
                startPolling() {
                    if (this._pollTimer) return;
                    this._pollTimer = setInterval(() => this.poll(), 15000);
                },
                stopPolling() {
                    if (this._pollTimer) {
                        clearInterval(this._pollTimer);
                        this._pollTimer = null;
                    }
                },
                async poll() {
                    try {
                        const res = await fetch('{{ route("display.state") }}');
                        if (res.ok) this.applyState(await res.json(), false);
                    } catch (_) {}
                },
                enableSound() {
                    this.soundOn = true;
                    this.speak('Suara pemanggilan aktif.');
                },
                playChime() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        if (ctx.state === 'suspended') ctx.resume();

                        const tone = (freq, start, dur) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.frequency.value = freq;
                            gain.gain.setValueAtTime(0.2, ctx.currentTime + start);
                            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + dur);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start(ctx.currentTime + start);
                            osc.stop(ctx.currentTime + start + dur);
                        };

                        tone(587.33, 0, 0.25);   // D5
                        tone(880.00, 0.2, 0.4);  // A5
                    } catch (_) {}
                },
                speak(text = null) {
                    if (! this.soundOn || ! this.current) return;

                    this.playChime();

                    const message = text ?? `Nomor ${this.current.number}, silakan menuju ${this.current.counter}`;

                    setTimeout(() => {
                        window.speechSynthesis.cancel();
                        setTimeout(() => {
                            const utterance = new SpeechSynthesisUtterance(message);
                            utterance.lang = 'id-ID';
                            utterance.rate = 0.95;

                            const voices = window.speechSynthesis.getVoices();
                            const idVoice = voices.find(v => v.lang && v.lang.toLowerCase().replace('_', '-').startsWith('id'));
                            if (idVoice) utterance.voice = idVoice;

                            window.speechSynthesis.speak(utterance);
                        }, 60);
                    }, 400);
                },
            };
        }
    </script>
</body>
</html>
