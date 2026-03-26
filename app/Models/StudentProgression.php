<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgression extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'current_activity_index',
        'progression_status',
        'started_at',
        'completed_at',
        'notes',
        'tracking_data',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'tracking_data' => 'json',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
