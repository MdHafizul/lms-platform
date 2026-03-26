<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSltSummary extends Model
{
    use HasFactory;

    protected $table = 'student_slt_summary';

    protected $fillable = [
        'enrollment_id',
        'total_activities',
        'completed_activities',
        'total_time_spent_minutes',
        'completion_percentage',
        'last_activity_at',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
            'summary' => 'json',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
