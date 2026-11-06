{{-- Sidebar shell: seluruh halaman admin pakai ini. Tidak ada topbar. --}}
<div class="min-h-dvh bg-canvas lg:flex">

    {{-- Toggle mobile --}}
    <input id="admin-nav-toggle" type="checkbox" class="peer sr-only">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-line bg-surface transition-transform duration-200 peer-checked:translate-x-0 lg:sticky lg:top-0 lg:h-dvh lg:translate-x-0">

        {{-- Brand --}}
        <div class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-line px-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 transition hover:opacity-90">
                <span class="grid size-7 place-items-center rounded-lg bg-accent text-xs font-bold text-white shadow-xs">Q</span>
                <span class="text-sm font-semibold tracking-tight">{{ config('app.name') }}</span>
                <span class="rounded-full bg-accent-soft px-2 py-0.5 text-[11px] font-medium text-accent">Admin</span>
            </a>
            <label for="admin-nav-toggle" class="cursor-pointer rounded-lg p-1.5 text-muted transition hover:bg-canvas lg:hidden" aria-label="Tutup menu">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </label>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 overflow-y-auto p-3">
            <p class="px-3 pb-2 pt-3 text-[11px] font-semibold uppercase tracking-wider text-muted-light">Menu</p>
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   @class([
                       'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                       'bg-accent-soft text-accent' => request()->routeIs('admin.dashboard'),
                       'text-muted hover:bg-canvas hover:text-ink' => ! request()->routeIs('admin.dashboard'),
                   ])>
                    <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('admin.staff') }}"
                   @class([
                       'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                       'bg-accent-soft text-accent' => request()->routeIs('admin.staff'),
                       'text-muted hover:bg-canvas hover:text-ink' => ! request()->routeIs('admin.staff'),
                   ])>
                    <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    <span>Staf</span>
                </a>
                <a href="{{ route('admin.counters') }}"
                   @class([
                       'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                       'bg-accent-soft text-accent' => request()->routeIs('admin.counters'),
                       'text-muted hover:bg-canvas hover:text-ink' => ! request()->routeIs('admin.counters'),
                   ])>
                    <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
                    <span>Loket</span>
                </a>
            </div>

            <p class="px-3 pb-2 pt-6 text-[11px] font-semibold uppercase tracking-wider text-muted-light">Pintasan</p>
            <div class="space-y-1">
                <a href="{{ route('kiosk') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted transition hover:bg-canvas hover:text-ink">
                    <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" /></svg>
                    <span>Kiosk</span>
                    <svg class="ml-auto size-3 text-muted-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                </a>
                <a href="{{ route('display') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted transition hover:bg-canvas hover:text-ink">
                    <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" /></svg>
                    <span>Display TV</span>
                    <svg class="ml-auto size-3 text-muted-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                </a>
            </div>
        </nav>

        {{-- Akun --}}
        <div class="shrink-0 border-t border-line p-3">
            <div class="flex items-center gap-3 rounded-xl px-2 py-2">
                <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-accent-soft text-xs font-semibold text-accent">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-xs font-semibold text-ink">{{ auth()->user()->name }}</span>
                    <span class="block text-[11px] text-muted">{{ ucfirst(auth()->user()->role ?? 'admin') }}</span>
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-muted transition hover:bg-skip/5 hover:text-skip active:scale-[0.98]">
                    <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <label for="admin-nav-toggle" class="fixed inset-0 z-30 hidden bg-ink/40 backdrop-blur-sm peer-checked:block lg:hidden" aria-hidden="true"></label>

    {{-- Konten --}}
    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Bar mobile --}}
        <header class="sticky top-0 z-20 flex h-14 items-center gap-3 border-b border-line bg-surface/95 px-4 backdrop-blur-md lg:hidden">
            <label for="admin-nav-toggle" class="cursor-pointer rounded-lg border border-line p-2 text-muted transition hover:text-ink" aria-label="Buka menu">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </label>
            <span class="text-sm font-semibold tracking-tight">{{ config('app.name') }}</span>
            <span class="rounded-full bg-accent-soft px-2 py-0.5 text-[11px] font-medium text-accent">Admin</span>
            <span class="ml-auto truncate text-xs text-muted">{{ auth()->user()->name }}</span>
        </header>

        @yield('admin-content')
    </div>
</div>
