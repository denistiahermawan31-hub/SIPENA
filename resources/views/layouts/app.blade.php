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
<body class="min-h-screen bg-cream bg-dot-grid-soft font-sans text-ink antialiased">
@hasSection('navbar')
    @yield('navbar')
@else
    <header class="sticky top-0 z-30 border-b border-primary-100/70 bg-white/85 backdrop-blur-md">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <x-sipena-logo class="h-9 w-9" />
                <span class="text-lg font-extrabold tracking-wide text-primary-700">SIPEN<span class="text-gold-500">A</span></span>
            </a>

            <nav class="hidden items-center gap-1.5 rounded-full bg-cream p-1 ring-1 ring-primary-100/70 md:flex">
                @foreach ([
                    'dashboard' => ['Beranda', route('dashboard')],
                    'penilaian' => ['Penilaian', route('penilaian.show')],
                    'riwayat' => ['Riwayat', route('riwayat.index')],
                    'rekap' => ['Rekap', route('rekap.index')],
                ] as $key => [$label, $url])
                    <a href="{{ $url }}" @class(['rounded-full px-4 py-1.5 text-sm font-semibold transition-all', $navActive === $key ? 'bg-white text-primary-700 shadow-sm ring-1 ring-primary-100' : 'text-muted hover:text-primary-700'])>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2.5">
                <div class="hidden items-center gap-2.5 sm:flex">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-xs font-extrabold text-white shadow-sm ring-2 ring-white">
                        {{ substr(auth()->user()->classRoom?->name ?? 'SA', 0, 3) }}
                    </span>
                    <div class="text-right leading-tight">
                        <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-medium text-muted">{{ auth()->user()->classRoom?->name ?? 'SuperAdmin' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-muted transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.664l1.1-1.1a.75.75 0 1 0-1.06-1.06l-2.5 2.5a.75.75 0 0 0 0 1.06l2.5 2.5a.75.75 0 1 0 1.06-1.06l-1.1-1.1h9.586A.75.75 0 0 0 19 10Z" clip-rule="evenodd"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- Bawah: ukuran laptop kecil / mobile --}}
        <nav class="flex gap-1 overflow-x-auto border-t border-primary-50 px-4 py-2 md:hidden">
            @foreach ([
                'dashboard' => ['Beranda', route('dashboard')],
                'penilaian' => ['Penilaian', route('penilaian.show')],
                'riwayat' => ['Riwayat', route('riwayat.index')],
                'rekap' => ['Rekap', route('rekap.index')],
            ] as $key => [$label, $url])
                <a href="{{ $url }}" @class(['shrink-0 rounded-full px-3 py-1.5 text-sm font-semibold', $navActive === $key ? 'bg-primary-50 text-primary-700' : 'text-muted'])>{{ $label }}</a>
            @endforeach
        </nav>
    </header>
@endif

<main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
    @if (session('status') && session('message'))
        <div @class(['mb-6 flex items-center gap-2.5 rounded-2xl border px-4 py-3 text-sm font-medium shadow-sm', session('status') === 'sukses' ? 'border-primary-200 bg-primary-50 text-primary-800' : 'border-red-200 bg-red-50 text-red-800'])>
            <svg class="h-4.5 w-4.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                @if (session('status') === 'sukses')
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                @else
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/>
                @endif
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-t border-primary-100/70 py-6 text-center text-xs font-medium text-muted/70">
    SIPENA &mdash; Sistem Penilaian Pembiasaan Harian &bull; Catatan keagamaan: sholat Duha &amp; Dzuhur, dan Al-Qur'an
</footer>

@stack('scripts')
</body>
</html>
