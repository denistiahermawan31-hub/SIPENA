<?php

namespace App\Services;

use App\Models\DailyReport;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class DeadlineService
{
    public const TIMEZONE = 'Asia/Jakarta';

    /** Waktu sekarang dalam WIB */
    public function now(): Carbon
    {
        return Carbon::now(static::TIMEZONE);
    }

    /**
     * Jumat 12:30 WIB minggu dari tanggal yang diberikan (default: minggu berjalan).
     */
    public function deadline(?Carbon $date = null): Carbon
    {
        $date = ($date ?? $this->now())->copy()->setTimezone(static::TIMEZONE);

        return $date->startOfWeek()->addDays(4)->setTime(12, 30);
    }

    /** Seluruh minggu (Senin-Jumat) terkunci otomatis setelah deadline */
    public function isWeekLocked(?Carbon $date = null): bool
    {
        return $this->now()->gt($this->deadline($date));
    }

    /**
     * Tanggal hari ini bila hari itu bisa diisi: Senin-Jumat dan sebelum Jumat 12:30 WIB.
     */
    public function todaysEditableDate(): ?Carbon
    {
        $today = $this->now();

        if (in_array($today->dayOfWeekIso, [CarbonInterface::SATURDAY, CarbonInterface::SUNDAY], true)) {
            return null;
        }

        if ($this->isWeekLocked()) {
            return null;
        }

        return $today;
    }

    /** Senin sampai Jumat dari minggu yang memuat tanggal yang diberikan */
    public function weekDays(?Carbon $date = null): array
    {
        $start = ($date ?? $this->now())->copy()->setTimezone(static::TIMEZONE)->startOfWeek();

        return collect(range(0, 4))
            ->map(fn (int $offset) => $start->copy()->addDays($offset))
            ->all();
    }

    /**
     * Apakah sebuah catatan harian boleh diedit:
     * - tanggalnya hari ini (Senin-Jumat, sebelum Jumat 12:30 WIB), atau
     * - ditandai "perlu perbaikan" (dibuka kembali oleh SuperAdmin).
     */
    public function canEdit(DailyReport $report): bool
    {
        if ($report->status === 'perlu_perbaikan') {
            return true;
        }

        return ! $this->isWeekLocked($report->report_date) && $this->isToday($report->report_date);
    }

    public function isToday(Carbon $date): bool
    {
        return $date->copy()->setTimezone(static::TIMEZONE)->isSameDay($this->now());
    }
}
