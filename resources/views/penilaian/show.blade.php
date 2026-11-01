@extends('layouts.app', ['navActive' => 'penilaian'])

@section('title', 'Penilaian Harian')

@section('content')
    @php
        $duhaOptions = config('sipena.options.duha');
        $dzuhurOptions = config('sipena.options.dzuhur');
        $quranOptions = config('sipena.options.quran');
        $sheets = [
            'duha' => [
                'label' => 'Sholat Duha',
                'options' => $duhaOptions,
                'legend' => 'Hadir +2 &bull; Munfarid +1 &bull; Haid 0 &bull; Tidak Hadir 0 &bull; Tidak Sekolah 0 &bull; Tidak Sholat &minus;2',
            ],
            'dzuhur' => [
                'label' => 'Sholat Dzuhur',
                'options' => $dzuhurOptions,
                'legend' => 'Hadir +2 &bull; Haid 0 &bull; Tidak Hadir 0 &bull; Tidak Sekolah 0 &bull; Tidak Sholat &minus;1',
            ],
            'quran' => [
                'label' => "Al-Qur'an",
                'options' => $quranOptions,
                'legend' => 'Bawa +2 &bull; Tidak Bawa &minus;1 &bull; Tidak Sekolah 0',
            ],
        ];
    @endphp

    <div class="grid gap-6">
        {{-- Kepala halaman --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-primary-600 hover:underline">&larr; Kembali ke beranda</a>
                <h1 class="mt-1 text-2xl font-bold text-primary-800">Penilaian Harian Kelas {{ auth()->user()->classRoom?->name ?? '—' }}</h1>
                <p class="mt-0.5 text-sm text-primary-600/80">{{ $selected->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if ($report)
                    <x-status-badge :status="$report->status" />
                @else
                    <span class="inline-flex items-center rounded-full bg-white px-2.5 py-0.5 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">Belum diisi</span>
                @endif
            </div>
        </div>

        {{-- Pemberitahuan keadaan --}}
        @if ($report && $report->status === 'perlu_perbaikan')
            <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-red-700">Hari ini ditandai &ldquo;Perlu Perbaikan&rdquo;</p>
                    @if ($report->verification_note)
                        <p class="mt-0.5 text-xs text-red-600/90">Catatan verifikasi: {{ $report->verification_note }}</p>
                    @endif
                    <p class="mt-0.5 text-xs text-red-600/90">Perbaiki data di bawah, lalu kirim ulang.</p>
                </div>
            </div>
        @elseif ($report && $report->status === 'diterima')
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                Data hari ini sudah diverifikasi dan <em>diterima</em>.
            </div>
        @elseif ($locked)
            <div class="rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-800">
                <span class="font-semibold">Status: Terkirim.</span> Data sudah terkunci dan tidak bisa diubah lagi
                @if ($report->submitted_at) (dikirim {{ $report->submitted_at->setTimezone('Asia/Jakarta')->format('d M Y H.i') }} WIB)@endif.
            </div>
        @endif

        @if (! $isWeekday)
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-4 text-center shadow-sm">
                <p class="text-sm font-semibold text-slate-600">Tanggal ini bukan hari sekolah</p>
                <p class="mt-1 text-xs text-slate-500">Penilaian hanya diisi Senin sampai Jumat.</p>
            </div>
        @elseif (! $editable)
            <div class="rounded-2xl border border-primary-100 bg-white p-8 text-center shadow-sm">
                <svg class="mx-auto h-10 w-10 text-primary-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 1 0-8 0v2h8Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p class="mt-3 text-sm font-bold text-primary-800">Formulir terkunci otomatis</p>
                <p class="mx-auto mt-1 max-w-md text-xs leading-relaxed text-primary-600/80">
                    Penilaian hanya dapat diisi pada hari yang sama dan seluruh minggu terkunci setelah Jumat 12.30 WIB.
                    Hubungi SuperAdmin bila data Anda ditandai untuk diperbaiki.
                </p>
            </div>
        @else
            {{-- Formulir penilaian: 3 sheet (Duha, Dzuhur, Al-Qur'an) --}}
            <form method="POST" action="{{ route('penilaian.save', $report?->report_date?->toDateString() ?? $selected->toDateString()) }}" id="form-penilaian">
                @csrf
                <input type="hidden" name="aksi" id="input-aksi" value="simpan">

                {{-- Tab sheet, melekat di atas saat scroll --}}
                <div class="sticky top-0 z-20 -mx-4 mb-4 bg-cream/90 px-4 pb-3 pt-2 backdrop-blur">
                    <div class="flex gap-1.5 rounded-2xl border border-primary-100 bg-white p-1.5 shadow-sm" role="tablist" id="tab-bar">
                        @foreach ($sheets as $key => $sheet)
                            <button type="button" role="tab" data-tab="{{ $key }}"
                                    @class(['tab-btn flex flex-1 items-center justify-center gap-1.5 rounded-xl px-2 py-2.5 text-xs font-semibold transition sm:text-sm', $loop->first ? 'bg-primary-700 text-white shadow' : 'text-primary-600 hover:bg-primary-50'])>
                                {{ $sheet['label'] }}
                                <span data-badge="{{ $key }}" class="hidden rounded-full bg-red-100 px-1.5 text-[11px] font-bold text-red-700"></span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-4">
                    @foreach ($sheets as $key => $sheet)
                        <section data-panel="{{ $key }}" @class(['sheet rounded-2xl border border-primary-100 bg-white shadow-sm', $loop->first ? '' : 'hidden'])>
                            <div class="flex items-center justify-between gap-2 border-b border-primary-50 px-4 py-3">
                                <p class="text-sm font-bold text-primary-800">{{ $sheet['label'] }}</p>
                                <p class="text-[11px] text-primary-500">{{ $students->count() }} siswa</p>
                            </div>
                            <p class="border-b border-primary-50 bg-primary-50/40 px-4 py-2 text-[11px] leading-relaxed text-primary-600/90">
                                {!! $sheet['legend'] !!}
                            </p>
                            <div class="divide-y divide-primary-50">
                                @foreach ($students as $student)
                                    @php($existing = $assessments->get($student->id))
                                    <div class="flex items-center gap-3 px-4 py-3">
                                        <span class="w-5 shrink-0 text-center text-xs text-primary-500 tabular-nums">{{ $loop->iteration }}</span>
                                        <span class="min-w-0 flex-1 truncate text-sm font-medium text-primary-900">{{ $student->name }}</span>
                                        <div class="w-36 shrink-0 sm:w-44">
                                            <x-evaluation-select name="nilai[{{ $student->id }}][{{ $key }}]"
                                                :options="$sheet['options']"
                                                :current="old("nilai.{$student->id}.{$key}", $existing?->$key)" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    {{-- Tadarus disembunyikan (aturan masih ditunda): dikirim sebagai "tidak" agar skor tidak berubah --}}
                    @foreach ($students as $student)
                        <input type="hidden" name="nilai[{{ $student->id }}][tadarus]"
                               value="{{ old("nilai.{$student->id}.tadarus", $assessments->get($student->id)?->tadarus ?? 'tidak') }}">
                    @endforeach
                </div>

                <div class="mt-3 rounded-xl border border-primary-100 bg-white px-4 py-3 text-xs text-primary-600/80 shadow-sm">
                    Isi ketiga sheet (Duha, Dzuhur, Al-Qur&#8209;an) untuk semua siswa sebelum menekan <span class="font-semibold text-primary-700">Kirim Data Harian</span>.
                    <span class="text-primary-700">Simpan Draf</span> bisa dipakai berkali-kali dan tidak mengunci data; <span class="font-semibold text-primary-700">Kirim</span> mengunci data hari ini.
                </div>

                {{-- Batang aksi tetap --}}
                <div class="sticky bottom-4 mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-primary-100 bg-white/95 px-5 py-4 shadow-lg backdrop-blur">
                    <p class="text-xs text-primary-600/80">
                        <span class="font-semibold text-primary-700">Kirim</span> mengunci data — periksa dulu badge merah per sheet.
                    </p>
                    <div class="flex gap-2">
                        <button type="submit" onclick="setAksi('simpan')"
                                class="rounded-xl border border-primary-200 bg-white px-4 py-2.5 text-sm font-semibold text-primary-700 transition hover:bg-primary-50">
                            Simpan Draf
                        </button>
                        <button type="submit" onclick="setAksi('kirim')" id="btn-kirim"
                                class="rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow transition hover:bg-gold-600">
                            Kirim Data Harian
                        </button>
                    </div>
                </div>
            </form>
        @endif

        {{-- Untuk data terkunci: tampilkan rincian per sheet --}}
        @if (! $editable && ($locked || ($report && $report->status === 'diterima')))
            <div class="grid gap-4">
                @foreach ($sheets as $key => $sheet)
                    <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
                        <p class="border-b border-primary-50 px-5 py-3 text-sm font-bold text-primary-800">{{ $sheet['label'] }}</p>
                        <div class="divide-y divide-primary-50">
                            @forelse ($students as $student)
                                @php($existing = $assessments->get($student->id))
                                <div class="flex items-center gap-3 px-5 py-2.5">
                                    <span class="w-5 shrink-0 text-center text-xs text-primary-500 tabular-nums">{{ $loop->iteration }}</span>
                                    <span class="min-w-0 flex-1 truncate text-sm font-medium text-primary-900">{{ $student->name }}</span>
                                    <span class="text-sm text-primary-700">{{ $existing?->$key ? $sheet['options'][$existing->$key] : '—' }}</span>
                                </div>
                            @empty
                                <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada data pada hari ini.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    var tabs = document.querySelectorAll('#tab-bar .tab-btn');

    function activateTab(key) {
        tabs.forEach(function (btn) {
            var active = btn.dataset.tab === key;
            btn.classList.toggle('bg-primary-700', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('shadow', active);
            btn.classList.toggle('text-primary-600', !active);
            btn.classList.toggle('hover:bg-primary-50', !active);
        });

        document.querySelectorAll('.sheet[data-panel]').forEach(function (panel) {
            panel.classList.toggle('hidden', panel.dataset.panel !== key);
        });
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateTab(btn.dataset.tab);
        });
    });

    function refreshSelect(select) {
        select.dataset.choice = select.value;
    }

    function refreshBadges() {
        document.querySelectorAll('.sheet[data-panel]').forEach(function (panel) {
            var empty = 0;
            panel.querySelectorAll('select.evaluasi').forEach(function (s) {
                if (!s.value) empty++;
            });
            var badge = document.querySelector('[data-badge="' + panel.dataset.panel + '"]');
            if (badge) {
                badge.textContent = empty;
                badge.classList.toggle('hidden', empty === 0);
            }
        });
    }

    function setAksi(aksi) {
        var input = document.getElementById('input-aksi');
        if (input) input.value = aksi;
        if (aksi === 'kirim') {
            var form = document.getElementById('form-penilaian');
            if (form) {
                form.querySelectorAll('select.evaluasi').forEach(function (s) {
                    if (!s.value) s.classList.add('border-red-400', 'bg-red-50');
                });
            }
        }
    }

    document.querySelectorAll('select.evaluasi').forEach(function (select) {
        refreshSelect(select);
        select.addEventListener('change', function () {
            select.classList.remove('border-red-400', 'bg-red-50');
            refreshSelect(select);
            refreshBadges();
        });
    });

    refreshBadges();
</script>
@endpush
