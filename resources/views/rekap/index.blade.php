@extends('layouts.app', ['navActive' => 'rekap'])

@section('title', 'Rekap Mingguan')

@section('content')
    @php
        $duhaOptions = config('sipena.options.duha');
        $dzuhurOptions = config('sipena.options.dzuhur');
        $quranOptions = config('sipena.options.quran');
        $prev = $monday->copy()->subDays(3)->startOfWeek()->toDateString();
        $next = $monday->copy()->addDays(10)->startOfWeek()->toDateString();
    @endphp

    <div class="grid gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-primary-800">Rekap Mingguan</h1>
                <p class="mt-0.5 text-sm text-primary-600/80">
                    Minggu {{ $monday->translatedFormat('d M') }} &ndash; {{ $weekDays->last()->translatedFormat('d F Y') }}
                    &bull; terbentuk otomatis dari kiriman harian kelas {{ auth()->user()->classRoom?->name ?? '—' }}
                </p>
            </div>
            <div class="flex items-center gap-1.5">
                <a href="{{ route('rekap.index', ['minggu' => $prev]) }}"
                   class="rounded-lg border border-primary-200 bg-white px-3 py-2 text-sm font-semibold text-primary-700 transition hover:bg-primary-50">&larr; Sebelumnya</a>
                @if ($canGoNext)
                    <a href="{{ route('rekap.index', ['minggu' => $next]) }}"
                       class="rounded-lg border border-primary-200 bg-white px-3 py-2 text-sm font-semibold text-primary-700 transition hover:bg-primary-50">Berikutnya &rarr;</a>
                @endif
            </div>
        </div>

        {{-- Ringkasan kelengkapan minggu --}}
        <div class="flex flex-wrap gap-2">
            @foreach ($weekDays as $day)
                @php($dayReport = $reports->get($day->toDateString()))
                <div class="flex items-center gap-2 rounded-xl border border-primary-100 bg-white px-3.5 py-2 shadow-sm">
                    <span class="text-xs font-semibold text-primary-700">{{ $day->translatedFormat('D') }}</span>
                    @if ($dayReport)
                        <x-status-badge :status="$dayReport->status" />
                    @else
                        <span class="inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-400 ring-1 ring-slate-200">—</span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Tabel rekap per siswa --}}
        <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                        <tr class="border-b border-primary-50 bg-primary-700/5 text-left text-xs font-semibold text-primary-700 uppercase">
                            <th class="sticky left-0 z-10 bg-primary-700/5 px-4 py-3 backdrop-blur">Siswa</th>
                            @foreach ($weekDays as $day)
                                <th class="px-3 py-3 text-center">
                                    <span class="block">{{ $day->translatedFormat('l') }}</span>
                                    <span class="mt-0.5 block text-[10px] font-medium text-primary-600/70 normal-case">{{ $day->format('j M') }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-50">
                        @forelse ($students as $student)
                            <tr class="transition-colors hover:bg-primary-50/30">
                                <td class="sticky left-0 z-10 bg-white px-4 py-2.5 font-medium text-primary-900 transition-colors group-hover:bg-primary-50">{{ $student->name }}</td>
                                @foreach ($weekDays as $day)
                                    @php($assessment = $grid[$student->id][$day->toDateString()] ?? null)
                                    <td class="px-3 py-2.5">
                                        @if ($assessment)
                                            <div class="flex items-center justify-center gap-1.5">
                                                <span class="rekap-dot dot-{{ $assessment->duha ?? 'none' }}"
                                                      title="Sholat Duha: {{ $duhaOptions[$assessment->duha] ?? '—' }}"></span>
                                                <span class="rekap-dot dot-{{ $assessment->dzuhur ?? 'none' }}"
                                                      title="Sholat Dzuhur: {{ $dzuhurOptions[$assessment->dzuhur] ?? '—' }}"></span>
                                                <span class="rekap-dot dot-{{ $assessment->quran ?? 'none' }}"
                                                      title="Al-Qur'an: {{ $quranOptions[$assessment->quran] ?? '—' }}"></span>
                                            </div>
                                        @else
                                            <span class="block text-center text-xs text-slate-300">&ndash;</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-xs text-slate-400">Belum ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Keterangan warna --}}
        <div class="rounded-2xl border border-primary-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold tracking-wide text-primary-700 uppercase">Keterangan</p>
            <div class="mt-3 grid gap-x-8 gap-y-2 text-xs text-primary-700/80 sm:grid-cols-3">
                <div>
                    <p class="font-semibold">Sholat Duha (titik 1)</p>
                    <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-hadir"></span>Hadir</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-munfarid"></span>Munfarid</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-haid"></span>Haid</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-tidak_hadir"></span>Tidak Hadir</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-tidak_sekolah"></span>Tidak Sekolah</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-tidak_sholat"></span>Tidak Sholat</span>
                    </p>
                </div>
                <div>
                    <p class="font-semibold">Sholat Dzuhur (titik 2)</p>
                    <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-hadir"></span>Hadir</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-haid"></span>Haid</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-tidak_hadir"></span>Tidak Hadir</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-tidak_sekolah"></span>Tidak Sekolah</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-tidak_sholat"></span>Tidak Sholat</span>
                    </p>
                </div>
                <div>
                    <p class="font-semibold">Al&#8209;Qur'an (titik 3)</p>
                    <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-bawa"></span>Bawa</span>
                        <span class="flex items-center gap-1.5"><span class="rekap-dot dot-tidak_bawa"></span>Tidak Bawa</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
