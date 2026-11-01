<?php

namespace App\Services;

use App\Models\Setting;

class ScoringService
{
    /** Bobot tadarus "ikut" diatur Admin (settings), default dari config */
    public function tadarusWeight(): int
    {
        $value = Setting::get('tadarus_weight', (string) config('sipena.scores.tadarus.ikut'));

        return max(0, (int) $value);
    }

    public function duhaScore(?string $value): int
    {
        return $value ? (int) config("sipena.scores.duha.$value", 0) : 0;
    }

    public function dzuhurScore(?string $value): int
    {
        return $value ? (int) config("sipena.scores.dzuhur.$value", 0) : 0;
    }

    public function quranScore(?string $value): int
    {
        return $value ? (int) config("sipena.scores.quran.$value", 0) : 0;
    }

    public function tadarusScore(?string $value): int
    {
        return $value === 'ikut' ? $this->tadarusWeight() : 0;
    }

    /** Hitung skor tiap komponen + total untuk satu baris penilaian */
    public function compute(?string $duha, ?string $dzuhur, ?string $quran, ?string $tadarus): array
    {
        $duhaScore = $this->duhaScore($duha);
        $dzuhurScore = $this->dzuhurScore($dzuhur);
        $quranScore = $this->quranScore($quran);
        $tadarusScore = $this->tadarusScore($tadarus);

        return [
            'duha_score' => $duhaScore,
            'dzuhur_score' => $dzuhurScore,
            'quran_score' => $quranScore,
            'tadarus_score' => $tadarusScore,
            'total_score' => $duhaScore + $dzuhurScore + $quranScore + $tadarusScore,
        ];
    }

    /** Total skor sekumpulan penilaian (mis. satu hari/kelas) */
    public function totalOf(iterable $assessments): int
    {
        $total = 0;

        foreach ($assessments as $assessment) {
            $total += (int) $assessment->total_score;
        }

        return $total;
    }
}
