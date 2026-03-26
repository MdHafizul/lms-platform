<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptOptionSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_question_snapshot_id',
        'question_option_id',
        'option_snapshot',
        'seq',
    ];

    public function questionSnapshot(): BelongsTo
    {
        return $this->belongsTo(AttemptQuestionSnapshot::class);
    }

    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class);
    }
}
