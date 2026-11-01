<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\DailyReport;
use App\Models\Student;
use App\Services\DeadlineService;
use App\Services\ScoringService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssessmentController extends Controller
{
    public function showToday(Request $request, DeadlineService $deadlines)
    {
        return $this->renderPenilaian($request, null, $deadlines);
    }

    public function show(Request $request, string $date, DeadlineService $deadlines)
    {
        return $this->renderPenilaian($request, $this->resolveDate($date), $deadlines);
    }

    private function renderPenilaian(Request $request, ?Carbon $selected, DeadlineService $deadlines)
    {
        $user = $request->user();

        $selected ??= ($deadlines->todaysEditableDate() ?? $deadlines->now());

        $report = DailyReport::where('class_id', $user->class_id)
            ->whereDate('report_date', $selected->toDateString())
            ->first();

        $students = Student::where('class_id', $user->class_id)
            ->orderBy('sort_order')
            ->get();

        $assessments = collect();

        if ($report) {
            $assessments = Assessment::where('daily_report_id', $report->id)
                ->get()
                ->keyBy('student_id');
        }

        $locked = $report && $report->isLocked() && $report->status !== 'perlu_perbaikan';
        $editable = $report
            ? $deadlines->canEdit($report)
            : $this->isReasonablyEditable($selected, $deadlines);

        return view('penilaian.show', [
            'report' => $report,
            'students' => $students,
            'assessments' => $assessments,
            'selected' => $selected,
            'locked' => $locked,
            'editable' => $editable && ! $locked,
            'isToday' => $deadlines->isToday($selected),
            'weekLocked' => $deadlines->isWeekLocked($selected),
            'isWeekday' => ! in_array($selected->dayOfWeekIso, [6, 7], true),
        ]);
    }

    public function save(Request $request, string $date, DeadlineService $deadlines, ScoringService $scoring): RedirectResponse
    {
        $user = $request->user();
        $target = $this->resolveDate($date);

        $data = $request->validate([
            'aksi' => ['required', Rule::in(['simpan', 'kirim'])],
            'nilai' => ['required', 'array'],
            'nilai.*.duha' => ['required', Rule::in(array_keys(config('sipena.options.duha')))],
            'nilai.*.dzuhur' => ['required', Rule::in(array_keys(config('sipena.options.dzuhur')))],
            'nilai.*.quran' => ['required', Rule::in(array_keys(config('sipena.options.quran')))],
            'nilai.*.tadarus' => ['nullable', Rule::in(array_keys(config('sipena.options.tadarus')))],
        ], [
            'nilai.required' => 'Data penilaian tidak ditemukan.',
            'nilai.*.duha.required' => 'Sholat Duha wajib dipilih untuk semua siswa.',
            'nilai.*.dzuhur.required' => 'Sholat Dzuhur wajib dipilih untuk semua siswa.',
            'nilai.*.quran.required' => "Kondisi Al-Qur'an wajib dipilih untuk semua siswa.",
            'nilai.*.duha.in' => 'Status sholat Duha tidak valid.',
            'nilai.*.dzuhur.in' => 'Status sholat Dzuhur tidak valid.',
            'nilai.*.quran.in' => "Status Al-Qur'an tidak valid.",
            'nilai.*.tadarus.in' => 'Status tadarus tidak valid.',
        ]);

        $action = $data['aksi'];

        /** @var DailyReport|null $report */
        $report = DailyReport::where('class_id', $user->class_id)
            ->whereDate('report_date', $target->toDateString())
            ->first();

        if ($report && $report->isLocked() && $report->status !== 'perlu_perbaikan') {
            return redirect()->route('penilaian.details', $target->toDateString())
                ->with('status', 'gagal')
                ->with('message', 'Data hari ini sudah terkirim dan terkunci.');
        }

        $editableNow = $deadlines->isToday($target) && ! $deadlines->isWeekLocked($target);

        if (! $editableNow) {
            return redirect()->route('dashboard')
                ->with('status', 'gagal')
                ->with('message', 'Penilaian hanya bisa diisi pada hari yang bersangkutan, sebelum Jumat 12.30 WIB.');
        }

        $studentIds = Student::where('class_id', $user->class_id)->pluck('id')->all();

        foreach (array_keys($data['nilai']) as $postedId) {
            if (! in_array((int) $postedId, $studentIds, true)) {
                throw ValidationException::withMessages([
                    'nilai' => 'Terdapat data siswa yang tidak termasuk kelas Anda.',
                ]);
            }
        }

        $now = $deadlines->now();

        $report = DB::transaction(function () use ($report, $user, $target, $data, $action, $scoring, $now) {
            if (! $report) {
                $report = new DailyReport([
                    'class_id' => $user->class_id,
                    'report_date' => $target->toDateString(),
                    'status' => 'draft',
                ]);
                $report->save();
            }

            foreach ($data['nilai'] as $studentId => $row) {
                $scores = $scoring->compute($row['duha'], $row['dzuhur'], $row['quran'], $row['tadarus'] ?? 'tidak');

                Assessment::updateOrCreate(
                    [
                        'daily_report_id' => $report->id,
                        'student_id' => (int) $studentId,
                    ],
                    array_merge([
                        'duha' => $row['duha'],
                        'dzuhur' => $row['dzuhur'],
                        'quran' => $row['quran'],
                        'tadarus' => $row['tadarus'] ?? 'tidak',
                    ], $scores)
                );
            }

            if ($action === 'kirim') {
                $report->status = 'terkirim';
                $report->submitted_at = $now;
                $report->verified_at = null;
            } else {
                $report->status = $report->status === 'perlu_perbaikan' ? 'perlu_perbaikan' : 'draft';
            }

            $report->save();

            return $report;
        });

        if ($action === 'kirim') {
            return redirect()->route('riwayat.index')
                ->with('status', 'sukses')
                ->with('message', "Data harian {$target->translatedFormat('d F Y')} berhasil dikirim. Datanya sudah terkunci.");
        }

        return redirect()->route('penilaian.details', $target->toDateString())
            ->with('status', 'sukses')
            ->with('message', 'Draf tersimpan. Jangan lupa kirim data harian sebelum deadline.');
    }

    private function resolveDate(string $date): Carbon
    {
        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $date, config('app.timezone'));
        } catch (\Throwable) {
            abort(404);
        }

        if (! $parsed || $parsed->format('Y-m-d') !== $date) {
            abort(404);
        }

        return $parsed->startOfDay();
    }

    private function isReasonablyEditable(Carbon $selected, DeadlineService $deadlines): bool
    {
        return $deadlines->isToday($selected)
            && ! $deadlines->isWeekLocked()
            && ! in_array($selected->dayOfWeekIso, [6, 7], true);
    }
}
