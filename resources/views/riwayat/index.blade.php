@extends('layouts.app', ['navActive' => 'riwayat'])

@section('title', 'Riwayat')

@section('content')
    <div class="grid gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-primary-800">Riwayat Kiriman</h1>
                <p class="mt-0.5 text-sm text-primary-600/80">Status penilaian harian kelas {{ auth()->user()->classRoom?->name ?? 'â€”' }} dan hasil verifikasi SuperAdmin.</p>
            </div>
            @if ($todayEditable)
                <a href="{{ route('penilaian.show') }}"
                   class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">
                    Isi penilaian hari ini
                </a>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-primary-100 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-primary-50 bg-primary-700/5 text-left text-xs font-semibold text-primary-700 uppercase">
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Dikirim</th>
                        <th class="px-4 py-3">Catatan Verifikasi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-50">
                    @forelse ($reports as $report)
                        <tr class="transition-colors hover:bg-primary-50/30">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-primary-900">{{ $report->report_date->translatedFormat('l') }}, {{ $report->report_date->translatedFormat('d F Y') }}</p>
                            </td>
                            <td class="px-4 py-3"><x-status-badge :status="$report->status" /></td>
                            <td class="px-4 py-3 text-xs text-primary-600/80">
                                {{ $report->submitted_at?->setTimezone('Asia/Jakarta')->format('d M Y, H.i') ?? 'â€”' }}
                            </td>
                            <td class="max-w-xs px-4 py-3">
                                @if ($report->verification_note)
                                    <p class="text-xs leading-relaxed text-slate-600">{{ $report->verification_note }}</p>
                                @else
                                    <span class="text-xs text-slate-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('penilaian.details', $report->report_date->toDateString()) }}"
                                   @class(['text-xs font-semibold underline-offset-2 hover:underline', $report->status === 'perlu_perbaikan' ? 'text-red-600' : 'text-primary-700'])>
                                    @if ($report->status === 'perlu_perbaikan') Perbaiki @else Lihat @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-sm font-semibold text-slate-500">Belum ada riwayat</p>
                                <p class="mt-1 text-xs text-slate-400">Kiriman harian kelas Anda akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="">{{ $reports->links() }}</div>
    </div>
@endsection
