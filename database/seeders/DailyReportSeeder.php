<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\DailyReport;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DailyReportSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(20261031);

        $now = Carbon::now('Asia/Jakarta');
        $currentMonday = $now->copy()->startOfWeek();
        $previousMonday = $currentMonday->copy()->subDays(7);

        DB::transaction(function () use ($currentMonday, $previousMonday, $now) {
            foreach (ClassRoom::query()->orderBy('name')->get() as $class) {
                $students = Student::where('class_id', $class->id)->orderBy('sort_order')->get();

                $this->seedWeek($class, $students, $previousMonday, $now, true, false);
                $this->seedWeek($class, $students, $currentMonday, $now, false, true);
            }
        });
    }

    /**
     * Isi laporan dummy satu minggu (Senin-Jumat) untuk satu kelas.
     * Minggu sebelumnya: semua hari DITERIMA (contoh hasil verifikasi SuperAdmin).
     * Minggu berjalan: hari sebelum hari ini TERKIRIM; dua kelas contoh ditandai PERLU PERBAIKAN.
     */
    private function seedWeek(ClassRoom $class, $students, Carbon $monday, Carbon $now, bool $accepted, bool $allowFlagged): void
    {
        $classFlagged = in_array($class->name, ['XI.2', 'XII.3'], true);
        $flagUsed = false;

        foreach (range(0, 4) as $offset) {
            $date = $monday->copy()->addDays($offset);

            if (! $accepted && $date->gte($now->copy()->startOfDay())) {
                continue; // hari ini dan seterusnya dibiarkan kosong agar bisa dipraktikkan
            }

            $latestSeedable = ! $accepted && $date->copy()->addDay()->startOfDay()->gte($now->startOfDay());

            $this->seedDay(
                class: $class,
                students: $students,
                date: $date,
                accepted: $accepted,
                flagged: $allowFlagged && $classFlagged && $latestSeedable && ! $flagUsed,
            );

            if ($allowFlagged && $classFlagged && $latestSeedable) {
                $flagUsed = true;
            }
        }
    }

    private function seedDay(ClassRoom $class, $students, Carbon $date, bool $accepted, bool $flagged): void
    {
        $report = DailyReport::firstOrCreate(
            ['class_id' => $class->id, 'report_date' => $date->toDateString()],
            ['status' => 'draft']
        );

        $report->status = $flagged ? 'perlu_perbaikan' : ($accepted ? 'diterima' : 'terkirim');
        $report->submitted_at = $date->copy()->setTime(15, 30);
        $report->verified_at = $flagged || $accepted ? $date->copy()->addDay()->setTime(9, mt_rand(10, 50)) : null;
        $report->verification_note = $flagged
            ? 'Data tadarus sebagian siswa belum sesuai catatan wali kelas. Mohon periksa dan kirim ulang.'
            : null;
        $report->save();

        $report->assessments()->delete();

        $duhaPool = ['hadir', 'hadir', 'hadir', 'hadir', 'munfarid', 'munfarid', 'haid', 'tidak_hadir', 'tidak_sholat', 'tidak_sekolah'];
        $dzuhurPool = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'haid', 'tidak_hadir', 'tidak_sholat'];
        $quranPool = ['bawa', 'bawa', 'bawa', 'bawa', 'tidak_bawa', 'tidak_sekolah'];

        $duhaScores = config('sipena.scores.duha');
        $dzuhurScores = config('sipena.scores.dzuhur');
        $quranScores = config('sipena.scores.quran');

        $rows = [];

        foreach ($students as $student) {
            $duha = $duhaPool[mt_rand(0, count($duhaPool) - 1)];
            $dzuhur = $dzuhurPool[mt_rand(0, count($dzuhurPool) - 1)];
            $quran = $quranPool[mt_rand(0, count($quranPool) - 1)];

            if ($duha === 'tidak_sekolah') {
                $dzuhur = 'tidak_sekolah';
                $quran = 'tidak_sekolah';
            }

            $duhaScore = $duhaScores[$duha];
            $dzuhurScore = $dzuhurScores[$dzuhur];
            $quranScore = $quranScores[$quran];

            $rows[] = [
                'daily_report_id' => $report->id,
                'student_id' => $student->id,
                'duha' => $duha,
                'dzuhur' => $dzuhur,
                'quran' => $quran,
                'tadarus' => 'tidak', // aturan penilaian tadarus masih ditunda
                'duha_score' => $duhaScore,
                'dzuhur_score' => $dzuhurScore,
                'quran_score' => $quranScore,
                'tadarus_score' => 0,
                'total_score' => $duhaScore + $dzuhurScore + $quranScore,
                'created_at' => $report->submitted_at,
                'updated_at' => $report->submitted_at,
            ];
        }

        DB::table('assessments')->insert($rows);
    }
}
