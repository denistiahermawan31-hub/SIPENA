<?php

namespace App\Models;

use Database\Factories\DailyReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

#[Fillable(['class_id', 'report_date', 'status', 'submitted_at', 'verified_at', 'verification_note'])]
class DailyReport extends Model
{
    /** @use HasFactory<DailyReportFactory> */
    use HasFactory;

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function scopeForClass(Builder $query): Builder
    {
        return $query->where('class_id', Auth::user()?->class_id);
    }

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['terkirim', 'diterima'], true);
    }

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        'draft' => 'Belum Dikirim',
        'terkirim' => 'Terkirim',
        'diterima' => 'diterima',
        'perlu_perbaikan' => 'Perlu Perbaikan',
    ];
}
