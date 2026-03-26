<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttemptQuestionSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_attempt_id',
        'exam_question_id',
        'question_snapshot',
        'seq',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class);
    }

    public function optionSnapshots(): HasMany
    {
        return $this->hasMany(AttemptOptionSnapshot::class);
    }
}
