@extends('layouts.app', ['navActive' => 'dashboard'])

@section('title', 'Beranda')

@section('content')
    <div class="grid gap-6">
        @php
        $className = auth()->user()->classRoom?->name ?? '&mdash;';
        @endphp

        {{-- Kartu status hari ini --}}
        <section class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4 bg-gradient-to-r from-primary-700 to-primary-600 p-6 sm:p-8">
                <div>
                    <p class="text-sm font-medium text-white/75">Selamat datang, perwakilan kelas <span class="font-bold text-gold-300">{{ $className }}</span></p>
                    <h1 class="mt-1.5 text-2xl font-bold text-white sm:text-3xl">
                        {{ $today ? $today->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y') }}
                    </h1>
                    <p class="mt-1 text-sm text-white/80">
                        Catatan pembiasaan positif kelas {{ $className }}
                    </p>
                </div>
                <div class="hidden shrink-0 sm:block"><x-sipena-logo class="h-16 w-16 opacity-90" /></div>
            </div>

            <div class="grid gap-4 p-6 sm:p-8 md:grid-cols-[1fr_auto] md:items-center">
                @if ($weekLocked)
                    <div>
                        <p class="text-base font-semibold text-primary-900">Minggu ini sudah ditutup</p>
                        <p class="mt-1 text-sm text-primary-600/80">Formulir terkunci otomatis setelah Jumat 12.30 WIB. Menunggu rekap mingguan dan verifikasi.</p>
                    </div>
                @elseif ($today)
                    <div>
                        @if ($todayReport && $todayReport->status !== 'perlu_perbaikan')
                            <p class="text-base font-semibold text-primary-900">Data hari ini sudah dikirim</p>
                            <p class="mt-1 text-sm text-primary-600/80">
                                Terkirim pada {{ $todayReport->submitted_at?->setTimezone('Asia/Jakarta')->format('H.i') }} WIB.
                            </p>
                        @elseif ($todayReport && $todayReport->status === 'perlu_perbaikan')
                            <p class="text-base font-semibold text-red-600">Perlu perbaikan</p>
                            <p class="mt-1 text-sm text-primary-600/80">Hari ini ditandai untuk diperbaiki. Silakan perbarui lalu kirim ulang.</p>
                        @else
                            <p class="text-base font-semibold text-primary-900">Penilaian hari ini belum dikirim</p>
                            <p class="mt-1 text-sm text-primary-600/80">Isi penilaian sholat Duha, Dzuhur, dan Al-Qur'an seluruh siswa kelas Anda.</p>
                        @endif
                    </div>
                    <div>
                        @if ($todayReport && $todayReport->status !== 'perlu_perbaikan')
                            <a href="{{ route('penilaian.details', $today->toDateString()) }}"
                               class="inline-flex items-center gap-2 rounded-xl border border-primary-200 px-4 py-2.5 text-sm font-semibold text-primary-700 transition hover:bg-primary-50">
                                Lihat data harian
                            </a>
                        @else
                            <a href="{{ route('penilaian.show') }}"
                               class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">
                                @if ($todayReport && $todayReport->status === 'perlu_perbaikan') Perbaiki sekarang @else Isi penilaian sekarang @endif
                            </a>
                        @endif
                    </div>
                @else
                    <div>
                        <p class="text-base font-semibold text-primary-900">Hari ini tidak ada penilaian</p>
                        <p class="mt-1 text-sm text-primary-600/80">Isi formulir hanya berlaku Senin sampai Jumat pada hari yang sama.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Info deadline --}}
        <section class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gold-200 bg-gold-50 px-5 py-4">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-gold-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.5a.75.75 0 0 0-1.5 0v4.19l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V6.5Z" clip-rule="evenodd"/></svg>
                <p class="text-sm font-medium text-gold-800">
                    <span class="font-bold">Deadline:</span> Jumat, {{ $deadline->translatedFormat('d F Y') }} jam <span class="font-semibold">12.30 WIB</span>
                </p>
            </div>
            <p class="text-sm text-gold-700">
                @if ($weekLocked)
                    <span class="font-semibold">Waktu sudah berlalu &mdash; formulir terkunci</span>
                @else
                    Sisa waktu: <span class="font-semibold">{{ intval($deadlineInMinutes / 1440) }} hari {{ intval(($deadlineInMinutes % 1440) / 60) }} jam {{ $deadlineInMinutes % 60 }} menit</span>
                @endif
            </p>
        </section>

        {{-- Linimasa mingguan --}}
        <section>
            <h2 class="mb-3 text-sm font-bold tracking-wide text-primary-700 uppercase">Minggu ini (Senin &ndash; Jumat)</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($weekDays as $day)
                    @php
                        $dateKey = $day->toDateString();
                        $report = $reportsByDate->get($dateKey);
                        $isToday = $today && $day->isSameDay($today);
                        $isPast = $day->lt(now()->startOfDay());
                        $linkable = $report && ($report->status !== 'draft' || $day->isSameDay(now())) || ($isToday && ! $weekLocked);
                    @endphp
                    <div @class(['rounded-xl border bg-white p-4 shadow-sm transition hover:shadow',
                                $isToday ? 'border-primary-500 ring-2 ring-primary-200' : 'border-primary-100'])>
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold {{ $isToday ? 'text-gold-700' : 'text-primary-600/70' }}">
                                {{ $day->translatedFormat('l') }}
                                @if ($isToday)<span class="ml-1 rounded-full bg-gold-100 px-1.5 py-0.5 text-[10px] text-gold-700">HARI INI</span>@endif
                            </p>
                            <p class="text-2xl font-bold text-primary-700 leading-none">{{ $day->format('j') }}</p>
                        </div>
                        <p class="mt-0.5 text-[11px] text-primary-600/60">{{ $day->translatedFormat('F Y') }}</p>

                        <div class="mt-3">
                            @if ($report)
                                <x-status-badge :status="$report->status" />
                            @elseif ($isPast)
                                <span class="inline-block rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-400 ring-1 ring-slate-200">Tidak diisi</span>
                            @else
                                <span class="inline-block rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">Belum diisi</span>
                            @endif
                        </div>

                        <a href="{{ route('penilaian.details', $dateKey) }}" @class(['mt-3 inline-flex items-center gap-1 text-xs font-semibold', $isToday && ! $weekLocked ? 'text-primary-700 underline-offset-2 hover:underline' : 'text-primary-600/60 underline-offset-2 hover:underline'])>
                            @if ($report && $report->status === 'perlu_perbaikan') Perbaiki data
                            @elseif ($report) Lihat detail
                            @elseif ($isToday && ! $weekLocked) Isi sekarang
                            @else Lihat @endif
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Panduan singkat --}}
        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-primary-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-primary-700">1. Isi harian oleh perwakilan kelas</p>
                <p class="mt-1 text-xs leading-relaxed text-primary-600/80">Setiap hari sekolah (Senin&ndash;Jumat) isi penilaian sholat Duha, Dzuhur, dan Al-Qur'an untuk seluruh siswa kelas.</p>
            </div>
            <div class="rounded-xl border border-primary-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-primary-700">2. Kirim pada hari itu</p>
                <p class="mt-1 text-xs leading-relaxed text-primary-600/80">Saat dikirim, status menjadi <span class="font-semibold">Terkirim</span> dan datanya langsung terkunci. Admin akan memverifikasi: <span class="font-semibold">Diterima</span> atau <span class="font-semibold text-red-600">Perlu Perbaikan</span>.</p>
            </div>
            <div class="rounded-xl border border-primary-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-primary-700">3. Rekap &amp; juara mingguan</p>
                <p class="mt-1 text-xs leading-relaxed text-primary-600/80">Rekap mingguan terbentuk otomatis. Satu kelas tereligius terpilih per tingkat (X, XI, XII) berdasarkan total nilai.</p>
            </div>
        </section>
    </div>
@endsection
