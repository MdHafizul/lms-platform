<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivityTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'learning_activity_id',
        'viewed_at',
        'completed_at',
        'time_spent_minutes',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'json',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function learningActivity(): BelongsTo
    {
        return $this->belongsTo(LearningActivity::class);
    }
}
