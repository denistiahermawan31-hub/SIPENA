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
<div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-primary-700 via-primary-600 to-primary-500 px-4 py-10">
    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl md:grid-cols-2">

        <div class="relative hidden flex-col justify-between bg-primary-700 p-10 text-white md:flex">
            <div>
                <div class="flex items-center gap-3">
                    <x-sipena-logo class="h-12 w-12" />
                    <div>
                        <p class="text-2xl font-bold tracking-wide">SIPEN<span class="text-gold-300">A</span></p>
                        <p class="text-xs text-white/70">Sistem Penilaian Pembiasaan Harian</p>
                    </div>
                </div>
                <p class="mt-8 text-lg leading-relaxed font-medium text-white/90">
                    Catat pembiasaan positif kelas harian:
                    <span class="text-gold-300">sholat Duha</span>,
                    <span class="text-gold-300">Dzuhur</span>, dan
                    <span class="text-gold-300">Al-Qur'an</span> &mdash; Senin sampai Jumat.
                </p>
            </div>
            <ul class="space-y-2.5 text-sm text-white/80">
                <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span> Kirim data hari itu, data langsung terkunci</li>
                <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span> Deadline Jumat 12.30 WIB</li>
                <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span> Rekap mingguan terbentuk otomatis</li>
            </ul>
        </div>

        <div class="p-8 sm:p-10">
            <div class="mx-auto max-w-sm">
                <div class="mb-8 flex items-center gap-3 md:hidden">
                    <x-sipena-logo class="h-10 w-10" />
                    <p class="text-xl font-bold text-primary-700">SIPEN<span class="text-gold-500">A</span></p>
                </div>

                <h1 class="text-xl font-bold text-primary-800">Masuk</h1>
                <p class="mt-1 text-sm text-primary-600/80">Masuk sebagai perwakilan kelas untuk mengisi penilaian harian.</p>

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="username" class="mb-1.5 block text-sm font-semibold text-primary-800">Username</label>
                        <input id="username" name="username" type="text" required autofocus autocomplete="username"
                               value="{{ old('username') }}"
                               placeholder="cth: x1, xi2, xii3"
                               @class(['w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition-colors', $errors->has('username') ? 'border-red-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' : 'border-primary-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100'])>
                        @error('username')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-primary-800">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               @class(['w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition-colors', $errors->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-2 focus:ring-red-100' : 'border-primary-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100'])>
                        @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-300">
                        Masuk
                    </button>
                </form>

                <div class="mt-8 rounded-xl bg-cream p-4 text-xs text-primary-700/90">
                    <p class="mb-1 font-semibold text-primary-700">Akun demo (dummy)</p>
                    <p>Perwakilan: <code class="rounded bg-white px-1 py-0.5 font-mono">x1&hellip;x4</code>,
                        <code class="rounded bg-white px-1 py-0.5 font-mono">xi1&hellip;xi3</code>,
                        <code class="rounded bg-white px-1 py-0.5 font-mono">xii1&hellip;xii3</code> &bull; password: <code class="rounded bg-white px-1 py-0.5 font-mono">password</code></p>
                    <p class="mt-1">SuperAdmin: <code class="rounded bg-white px-1 py-0.5 font-mono">superadmin</code> &bull; password: <code class="rounded bg-white px-1 py-0.5 font-mono">password</code></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
