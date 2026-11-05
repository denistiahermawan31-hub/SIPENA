<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SIPENA</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-primary-900 antialiased">
<div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-primary-600 via-[#0d9a78] to-primary-700 px-4 py-10 sm:px-6">
    {{-- Blob dekoratif lembut di latar --}}
    <div class="pointer-events-none absolute -top-32 -left-24 h-96 w-96 rounded-full bg-[#34d399]/25 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-20 h-[28rem] w-[28rem] rounded-full bg-[#2dd4bf]/20 blur-3xl"></div>
    <div class="pointer-events-none absolute top-1/3 right-1/4 h-64 w-64 rounded-full bg-gold-400/15 blur-3xl"></div>

    <div class="animate-fade-up grid w-full max-w-5xl overflow-hidden rounded-[28px] bg-white shadow-card-hover ring-1 ring-white/40 md:grid-cols-2">

        {{-- Panel kiri: gradient + pattern --}}
        <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-primary-500/95 via-[#0d9a78] to-primary-700 p-10 text-white md:flex">
            <div class="dot-grid absolute inset-0 opacity-70" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -right-16 -bottom-16 h-64 w-64 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -right-8 -bottom-8 h-40 w-40 rounded-full border border-white/15" aria-hidden="true"></div>

            <div class="relative">
                <div class="flex items-center gap-3">
                    <div class="rounded-2xl bg-white/15 p-2 ring-1 ring-white/25 backdrop-blur">
                        <x-sipena-logo class="h-12 w-12" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold tracking-wide">SIPEN<span class="text-gold-300">A</span></p>
                        <p class="text-xs font-medium text-white/70">Sistem Penilaian Pembiasaan Harian</p>
                    </div>
                </div>

                <h2 class="mt-10 text-[28px] leading-snug font-extrabold text-white/95">
                    Catat pembiasaan positif kelas harian:<br>
                    <span class="text-gold-300">sholat Duha</span>,
                    <span class="text-gold-300">Dzuhur</span>, dan
                    <span class="text-gold-300">Al-Qur'an</span>
                </h2>
                <p class="mt-3 text-sm font-medium text-white/70">Senin sampai Jumat &mdash; cukup lima menit setiap hari.</p>
            </div>

            <ul class="relative space-y-3 text-sm">
                @foreach ([['Kirim data hari itu, data langsung terkunci', 'M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v2h8Z'],
                            ['Deadline pengumpulan Jumat 12.30 WIB', 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                            ['Rekap mingguan terbentuk otomatis', 'M3 3v5h5M3.05 13A9 9 0 1 0 6 5.3L3 8m6.5 1.5 3.5 3.5 5-5']] as [$text, $path])
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/12 ring-1 ring-white/20 backdrop-blur">
                            <svg class="h-4 w-4 text-gold-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
                            </svg>
                        </span>
                        <span class="font-medium text-white/85">{{ $text }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Panel kanan: form masuk --}}
        <div class="flex flex-col justify-center p-8 sm:p-10">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-8 flex items-center gap-3 md:hidden">
                    <x-sipena-logo class="h-10 w-10" />
                    <p class="text-xl font-extrabold text-primary-700">SIPEN<span class="text-gold-500">A</span></p>
                </div>

                <h1 class="text-2xl font-extrabold tracking-tight text-ink">Selamat datang <span aria-hidden="true">👋</span></h1>
                <p class="mt-1.5 text-sm leading-relaxed text-muted">Masuk sebagai perwakilan kelas untuk mengisi penilaian harian.</p>

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="username" class="mb-1.5 block text-xs font-bold tracking-wide text-muted uppercase">Username</label>
                        <div class="relative">
                            <svg class="absolute top-1/2 left-3.5 h-4.5 w-4.5 -translate-y-1/2 text-primary-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                            </svg>
                            <input id="username" name="username" type="text" required autofocus autocomplete="username"
                                   value="{{ old('username') }}"
                                   placeholder="cth: x1, xi2, xii3"
                                   @class(['w-full rounded-xl border bg-primary-50/50 py-2.5 pr-4 pl-10 text-sm font-medium text-ink placeholder:text-primary-300 outline-none transition-all', $errors->has('username') ? 'border-red-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' : 'border-primary-100 focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-100'])>
                        </div>
                        @error('username')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-xs font-bold tracking-wide text-muted uppercase">Password</label>
                        <div class="relative">
                            <svg class="absolute top-1/2 left-3.5 h-4.5 w-4.5 -translate-y-1/2 text-primary-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                            </svg>
                            <input id="password" name="password" type="password" required autocomplete="current-password" data-password-input
                                   @class(['w-full rounded-xl border bg-primary-50/50 py-2.5 pr-11 pl-10 text-sm font-medium text-ink outline-none transition-all', $errors->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' : 'border-primary-100 focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-100'])>
                            <button type="button" data-password-toggle
                                    class="absolute top-1/2 right-2.5 -translate-y-1/2 rounded-lg p-1.5 text-primary-300 transition-colors hover:text-primary-600"
                                    aria-label="Tampilkan atau sembunyikan password">
                                {{-- Ikon mata (status tersembunyi) --}}
                                <svg data-icon-show class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                    <path fill-rule="evenodd" d="M.664 10.09a15.05 15.05 0 0 1 18.672 0c-.484 3.579-2.435 6.38-4.083 7.996A9.94 9.94 0 0 1 10 19.75c-1.726 0-3.407-.598-4.846-1.653-1.657-1.61-3.611-4.41-4.49-8.007Zm13.139-.994a3.75 3.75 0 1 1-7.606 0 3.75 3.75 0 0 1 7.606 0Z" clip-rule="evenodd" />
                                </svg>
                                {{-- Ikon mata coret (status terlihat) --}}
                                <svg data-icon-hide class="hidden h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        @error('password')<p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 px-4 py-3 text-sm font-bold text-white shadow-glow transition-all hover:-translate-y-0.5 hover:shadow-card-hover focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 active:translate-y-0">
                        Masuk ke Dashboard
                    </button>
                </form>

                <details class="group mt-6 rounded-xl bg-cream ring-1 ring-primary-100 open:ring-primary-200">
                    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-xs font-bold text-primary-700 select-none">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-primary-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M8.75 4.566a2 2 0 0 1 2.5 0l5.08 4.065a2 2 0 0 1 0 3.124l-5.08 4.065a2 2 0 0 1-3.25-1.562V6.128a2 2 0 0 1 .75-1.562Z" />
                            </svg>
                            Akun demo (dummy)
                        </span>
                        <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-180" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="m2.5 4.5 3.5 3.5 3.5-3.5" />
                        </svg>
                    </summary>
                    <div class="space-y-1.5 border-t border-primary-100/60 px-4 py-3 text-xs text-muted">
                        <p>Perwakilan:
                            <code class="rounded bg-white px-1.5 py-0.5 font-mono text-primary-700 ring-1 ring-primary-100">x1&hellip;x4</code>,
                            <code class="rounded bg-white px-1.5 py-0.5 font-mono text-primary-700 ring-1 ring-primary-100">xi1&hellip;xi3</code>,
                            <code class="rounded bg-white px-1.5 py-0.5 font-mono text-primary-700 ring-1 ring-primary-100">xii1&hellip;xii3</code>
                            &bull; password: <code class="rounded bg-white px-1.5 py-0.5 font-mono text-primary-700 ring-1 ring-primary-100">password</code></p>
                        <p>SuperAdmin: <code class="rounded bg-white px-1.5 py-0.5 font-mono text-primary-700 ring-1 ring-primary-100">superadmin</code>
                            &bull; password: <code class="rounded bg-white px-1.5 py-0.5 font-mono text-primary-700 ring-1 ring-primary-100">password</code></p>
                    </div>
                </details>

                <p class="mt-8 text-center text-[11px] text-muted/70">&copy; {{ now()->year }} SIPENA &mdash; Sistem Penilaian Pembiasaan Harian</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle show/hide password (tanpa framework)
    document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
        var input = btn.closest('div').querySelector('[data-password-input]');
        var iconShow = btn.querySelector('[data-icon-show]');
        var iconHide = btn.querySelector('[data-icon-hide]');
        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            iconShow.classList.toggle('hidden', show);
            iconHide.classList.toggle('hidden', !show);
            input.focus({ preventScroll: true });
        });
    });
</script>
</body>
</html>
