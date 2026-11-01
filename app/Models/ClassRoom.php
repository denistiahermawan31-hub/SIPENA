<?php

namespace App\Models;

use Database\Factories\ClassRoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'level'])]
class ClassRoom extends Model
{
    /** @use HasFactory<ClassRoomFactory> */
    use HasFactory;

    protected $table = 'classes';

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id')->orderBy('sort_order');
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class, 'class_id');
    }
}
