<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') SIPENA</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-primary-900 antialiased">
@hasSection('navbar')
    @yield('navbar')
@else
    <header class="border-b border-primary-100 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <x-sipena-logo class="h-9 w-9" />
                <span class="text-lg font-bold tracking-wide text-primary-700">SIPEN<span class="text-gold-500">A</span></span>
            </a>

            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('dashboard') }}" @class(['rounded-lg px-3.5 py-2 text-sm font-medium transition-colors', $navActive === 'dashboard' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70 hover:text-primary-700'])>Beranda</a>
                <a href="{{ route('penilaian.show') }}" @class(['rounded-lg px-3.5 py-2 text-sm font-medium transition-colors', $navActive === 'penilaian' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70 hover:text-primary-700'])>Penilaian</a>
                <a href="{{ route('riwayat.index') }}" @class(['rounded-lg px-3.5 py-2 text-sm font-medium transition-colors', $navActive === 'riwayat' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70 hover:text-primary-700'])>Riwayat</a>
                <a href="{{ route('rekap.index') }}" @class(['rounded-lg px-3.5 py-2 text-sm font-medium transition-colors', $navActive === 'rekap' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70 hover:text-primary-700'])>Rekap</a>
            </nav>

            <div class="flex items-center gap-2">
                <div class="mr-1 hidden text-right sm:block">
                    <p class="text-sm leading-tight font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-xs leading-tight text-primary-600">{{ auth()->user()->classRoom?->name ?? 'â€”' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-primary-200 px-3 py-1.5 text-sm font-medium text-primary-700 transition-colors hover:bg-primary-50">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
        <nav class="flex gap-1 overflow-x-auto border-t border-primary-50 px-4 py-2 md:hidden">
            <a href="{{ route('dashboard') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', $navActive === 'dashboard' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70'])>Beranda</a>
            <a href="{{ route('penilaian.show') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', $navActive === 'penilaian' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70'])>Penilaian</a>
            <a href="{{ route('riwayat.index') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', $navActive === 'riwayat' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70'])>Riwayat</a>
            <a href="{{ route('rekap.index') }}" @class(['shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium', $navActive === 'rekap' ? 'bg-primary-50 text-primary-700' : 'text-primary-700/70'])>Rekap</a>
        </nav>
    </header>
@endif

<main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
    @if (session('status') && session('message'))
        <div @class(['mb-6 rounded-xl border px-4 py-3 text-sm shadow-sm', session('status') === 'sukses' ? 'border-primary-200 bg-primary-50 text-primary-800' : 'border-red-200 bg-red-50 text-red-800'])>
            {{ session('message') }}
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-t border-primary-100 py-6 text-center text-xs text-primary-600">
    SIPENA &mdash; Sistem Penilaian Pembiasaan Harian &bull; Catatan keagamaan: sholat Duha &amp; Dzuhur, dan Al-Qur'an
</footer>

@stack('scripts')
</body>
</html>
