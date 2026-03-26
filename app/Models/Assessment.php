<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_activity_id',
        'assessment_type',
        'grading_type',
        'total_questions',
        'pass_score',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'json',
        ];
    }

    public function learningActivity(): BelongsTo
    {
        return $this->belongsTo(LearningActivity::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
