@extends('layouts.app', ['navActive' => 'dashboard'])

@section('title', 'Beranda')

@section('content')
    <div class="grid gap-6">
        @php
        $className = auth()->user()->classRoom?->name ?? '&mdash;';
        $nowId = now(); // timezone aplikasi: Asia/Jakarta
        $greeting = match (true) {
            $nowId->hour < 11 => 'Selamat pagi',
            $nowId->hour < 15 => 'Selamat siang',
            $nowId->hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
        $sentDays = $reportsByDate->filter(fn ($r) => $r->status !== 'draft')->count();
        $weekTotal = $weekDays->count();
        $progressPct = $weekTotal > 0 ? (int) round($sentDays / $weekTotal * 100) : 0;
        $dayIndex = $today ? $weekDays->search(fn ($d) => $d->isSameDay($today)) : false;
        $dayIndex = $dayIndex !== false ? $dayIndex + 1 : null;
        $urgent = ! $weekLocked && $deadlineInMinutes < 1440; // < 24 jam
        $todayMsg = match (true) {
            $weekLocked => 'Minggu ini sudah ditutup',
            $todayReport && $todayReport->status !== 'perlu_perbaikan' => 'Data hari ini sudah dikirim',
            $todayReport && $todayReport->status === 'perlu_perbaikan' => 'Hari ini perlu perbaikan',
            $today => 'Penilaian hari ini belum dikirim',
            default => 'Hari ini tidak ada penilaian',
        };
        @endphp

        {{-- Banner sambutan glassmorphic --}}
        <section class="animate-fade-up overflow-hidden rounded-3xl border border-primary-100/80 shadow-card">
            <div class="relative overflow-hidden bg-gradient-to-br from-primary-500 via-[#0d9a78] to-primary-700 p-6 text-white sm:p-8">
                <div class="dot-grid absolute inset-0 opacity-60" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -top-20 -right-16 h-56 w-56 rounded-full border border-white/10" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -top-10 -right-6 h-36 w-36 rounded-full border border-white/15" aria-hidden="true"></div>

                <div class="relative flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold tracking-[0.12em] text-white/70 uppercase">
                            {{ $today ? $today->translatedFormat('l, d F Y') : $nowId->translatedFormat('l, d F Y') }}
                            @if ($dayIndex)
                                <span class="ml-2 inline-flex items-center gap-1.5 rounded-full bg-gold-400/90 px-2.5 py-0.5 text-[10px] font-extrabold tracking-normal text-primary-900 normal-case align-middle">
                                    <span>HARI</span><span>KE-{{ $dayIndex }}</span><span>DARI</span><span>{{ $weekTotal }}</span></span>
                            @endif
                        </p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-[28px]">
                            {{ $greeting }}, <span class="text-gold-300">Kelas {{ $className }}</span> <span aria-hidden="true">{{ $nowId->hour < 18 && $nowId->hour >= 5 ? '☀️' : '🌙' }}</span>
                        </h1>
                        <p class="mt-1 text-sm font-medium text-white/75">Catatan pembiasaan positif kelas {{ $className }}</p>
                    </div>

                    {{-- Progres minggu --}}
                    <div class="glass-card min-w-[190px] rounded-2xl px-4 py-3">
                        <p class="text-[11px] font-bold tracking-wide text-white/75 uppercase">Progres minggu ini</p>
                        <p class="mt-1 text-2xl font-extrabold">{{ $sentDays }}<span class="text-sm font-semibold text-white/70">/{{ $weekTotal }} hari</span></p>
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-white/20">
                            <div class="h-full rounded-full bg-gradient-to-r from-gold-300 to-gold-400 transition-all" style="width: {{ $progressPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Strip status hari ini --}}
            <div class="grid gap-4 border-t border-primary-100/70 bg-white p-6 sm:p-8 md:grid-cols-[1fr_auto] md:items-center">
                <div>
                    <p class="text-base font-bold text-ink">{{ $todayMsg }}</p>

                    @if ($weekLocked)
                        <p class="mt-1 text-sm text-muted">Formulir terkunci otomatis setelah Jumat 12.30 WIB. Menunggu rekap mingguan dan verifikasi.</p>
                    @elseif ($today && $todayReport && $todayReport->status !== 'perlu_perbaikan')
                        <p class="mt-1 text-sm text-muted">Terkirim pada {{ $todayReport->submitted_at?->setTimezone('Asia/Jakarta')->format('H.i') }} WIB.</p>
                    @elseif ($today && $todayReport && $todayReport->status === 'perlu_perbaikan')
                        <p class="mt-1 text-sm text-muted">Hari ini ditandai admin untuk diperbaiki. Silakan perbarui lalu kirim ulang.</p>
                    @elseif ($today)
                        <p class="mt-1 text-sm text-muted">Isi penilaian sholat Duha, Dzuhur, dan Al-Qur'an seluruh siswa kelas Anda.</p>
                    @else
                        <p class="mt-1 text-sm text-muted">Isi formulir hanya berlaku Senin sampai Jumat pada hari yang sama.</p>
                    @endif
                </div>
                <div>
                    @if ($weekLocked)
                    @elseif ($today)
                        @if ($todayReport && $todayReport->status !== 'perlu_perbaikan')
                            <a href="{{ route('penilaian.details', $today->toDateString()) }}"
                               class="inline-flex items-center gap-2 rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm font-bold text-primary-700 shadow-card transition-all hover:-translate-y-0.5 hover:shadow-card-hover">
                                Lihat data harian
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M2 10a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm10.53-1.72a.75.75 0 1 0-1.06 1.06l1.19 1.16H6.75a.75.75 0 0 0 0 1.5h9.41l-1.19 1.16a.75.75 0 1 0 1.06 1.06l2.5-2.5a.75.75 0 0 0 0-1.06l-2.5-2.5v.08Z" clip-rule="evenodd"/></svg>
                            </a>
                        @else
                            <a href="{{ route('penilaian.show') }}"
                               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 px-5 py-2.5 text-sm font-bold text-white shadow-glow transition-all hover:-translate-y-0.5 hover:shadow-card-hover focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2">
                                @if ($todayReport && $todayReport->status === 'perlu_perbaikan') Perbaiki sekarang @else Isi penilaian sekarang @endif
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M2 10a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm10.53-1.72a.75.75 0 1 0-1.06 1.06l1.19 1.16H6.75a.75.75 0 0 0 0 1.5h9.41l-1.19 1.16a.75.75 0 1 0 1.06 1.06l2.5-2.5a.75.75 0 0 0 0-1.06l-2.5-2.5v.08Z" clip-rule="evenodd"/></svg>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </section>

        {{-- Panel deadline --}}
        <section @class(['animate-fade-up flex flex-wrap items-center justify-between gap-3 rounded-2xl border px-5 py-4 shadow-card transition-colors',
                        $urgent ? 'border-red-200 bg-red-50' : 'border-gold-200 bg-white'])>
            <div class="flex items-center gap-3">
                <span @class(['flex h-9 w-9 items-center justify-center rounded-xl ring-1',
                              $urgent ? 'bg-red-100 ring-red-200 text-red-600' : 'bg-gold-100 ring-gold-200 text-gold-600'])>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.5a.75.75 0 0 0-1.5 0v4.19l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V6.5Z" clip-rule="evenodd"/></svg>
                </span>
                <p class="text-sm font-medium {{ $urgent ? 'text-red-800' : 'text-gold-800' }}">
                    <span class="font-extrabold">Deadline:</span>
                    {{ $deadline->translatedFormat('l, d F Y') }} &bull; <span class="font-semibold">12.30 WIB</span>
                </p>
            </div>
            <p class="text-sm font-semibold {{ $urgent ? 'text-red-700' : 'text-gold-700' }}">
                @if ($weekLocked)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 ring-1 ring-slate-200">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>
                        Terkunci
                    </span>
                @else
                    Sisa waktu: {{ intval($deadlineInMinutes / 1440) }} hari {{ intval(($deadlineInMinutes % 1440) / 60) }} jam {{ $deadlineInMinutes % 60 }} menit
                @endif
            </p>
        </section>

        {{-- Linimasa mingguan --}}
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-extrabold tracking-wide text-ink uppercase">Minggu ini (Senin &ndash; Jumat)</h2>
                <span class="text-xs font-semibold text-muted">Klik kartu untuk membuka detail</span>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($weekDays as $day)
                    @php
                        $dateKey = $day->toDateString();
                        $report = $reportsByDate->get($dateKey);
                        $isToday = $today && $day->isSameDay($today);
                        $isPast = $day->lt(now()->startOfDay());
                        $isFuture = ! $isToday && ! $isPast && ! $report;
                        $needsFix = $report && $report->status === 'perlu_perbaikan';
                        $actionable = $isToday && ! $weekLocked && ($needsFix || ! $report);
                    @endphp
                    <a href="{{ route('penilaian.details', $dateKey) }}"
                       @class(['group relative flex flex-col rounded-2xl border bg-white p-4 transition-all duration-200 hover:-translate-y-1 hover:shadow-card-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-400 focus-visible:ring-offset-2',
                               $isToday && $actionable
                                   ? 'animate-pulse-ring border-gold-400 ring-2 ring-gold-200 shadow-card'
                                   : ($report && $report->status !== 'draft' && $report->status !== 'perlu_perbaikan'
                                       ? 'border-primary-200 bg-gradient-to-b from-primary-50/60 to-white shadow-card'
                                       : ($isToday && ! $actionable
                                           ? 'border-primary-200 ring-2 ring-primary-100 shadow-card'
                                           : 'border-slate-100 shadow-card')),
                               $isFuture && ! $isToday ? 'opacity-70' : ''])>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[11px] font-bold tracking-[0.08em] uppercase {{ $isToday ? 'text-gold-700' : ($isFuture && ! $isToday ? 'text-slate-400' : 'text-muted') }}">
                                    {{ $day->translatedFormat('l') }}
                                </p>
                                <p class="mt-1 text-[13px] font-extrabold tracking-tight text-ink">{{ str_pad($day->format('j'), 2, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-[11px] font-medium text-muted/70">{{ $day->translatedFormat('F Y') }}</p>
                            </div>
                            <span @class(['flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-sm font-extrabold',
                                          $isToday ? 'bg-gold-100 text-gold-700' : ($report && $report->status !== 'draft' && $report->status !== 'perlu_perbaikan' ? 'bg-primary-100 text-primary-700' : 'bg-slate-50 text-slate-400')])
                                  aria-hidden="true">{{ $day->format('j') }}</span>
                        </div>

                        @if ($isToday)
                            <span class="absolute -top-2 right-3 rounded-full bg-gold-400 px-2 py-0.5 text-[9px] font-extrabold tracking-wider text-primary-900 shadow-sm">HARI INI</span>
                        @endif

                        <div class="mt-3 border-t border-slate-100 pt-3">
                            @if ($report)
                                <x-status-badge :status="$report->status" />
                            @elseif ($isPast)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-400 ring-1 ring-slate-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Tidak diisi
                                </span>
                            @elseif ($isFuture && ! $isToday)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-400 ring-1 ring-slate-200">
                                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>
                                    Mendatang
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-50 px-2.5 py-0.5 text-xs font-bold text-gold-700 ring-1 ring-gold-200">
                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-gold-500"></span> Belum diisi
                                </span>
                            @endif
                        </div>

                        <div class="mt-2 inline-flex items-center gap-1 text-xs font-bold {{ $actionable ? 'text-primary-600' : ($isFuture && ! $isToday ? 'text-slate-400' : 'text-muted') }} group-hover:underline">
                            @if ($needsFix) Perbaiki data
                            @elseif ($report) Lihat detail
                            @elseif ($actionable) Isi sekarang
                            @else Lihat @endif
                            <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M1.5 6h9m-3.5-3.5L10.5 6 7 9.5"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Panduan singkat --}}
        <section class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['1. Isi harian oleh perwakilan kelas', 'Setiap hari sekolah (Senin–Jumat) isi penilaian sholat Duha, Dzuhur, dan Al-Qur\'an untuk seluruh siswa kelas.', 'M10 2.5a7.5 7.5 0 1 1-7.5 7.5h7.5v-7.5ZM11.5 2v7.5H19a7.5 7.5 0 0 0-7.5-7.5Z'],
                ['2. Kirim pada hari itu', 'Saat dikirim, status menjadi Terkirim dan datanya langsung terkunci. Admin akan memverifikasi: Diterima atau Perlu Perbaikan.', 'M9.345 1.94a.75.75 0 0 1 .189 1.045l-2.4 3.384 4.066-1.42a.75.75 0 0 1 .5 1.416L6.06 8.45l4.4 2.298a.75.75 0 0 1-.795 1.276l-4.03-2.103 1.66 3.9a.75.75 0 0 1-1.382.586l-2.4-5.63'],
                ['3. Rekap & juara mingguan', 'Rekap mingguan terbentuk otomatis. Satu kelas tereligius terpilih per tingkat (X, XI, XII) berdasarkan total nilai.', 'M15.212 1.016h-4.01L10.36 6.18h2.952l-3.92 5.277h2.19l-2.19 7.527 7.167-9.08h-3.06l3.516-4.884'],
            ] as [$title, $desc, $icon])
                <div class="card-soft group p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-card-hover">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600 ring-1 ring-primary-100 transition-colors group-hover:bg-primary-100">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-bold text-ink">{{ $title }}</p>
                    <p class="mt-1 text-xs leading-relaxed text-muted/80">{!! $desc !!}</p>
                </div>
            @endforeach
        </section>
    </div>
@endsection
