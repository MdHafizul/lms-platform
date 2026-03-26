<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'enrolled_at',
        'completed_at',
        'final_grade',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'final_grade' => 'float',
            'metadata' => 'json',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function classEnrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function progression(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentProgression::class);
    }

    public function activityTracking(): HasMany
    {
        return $this->hasMany(StudentActivityTracking::class);
    }

    public function certificate(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function sltSummary(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentSltSummary::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }
}
