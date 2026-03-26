<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lecturer_id',
        'title',
        'description',
        'code',
        'duration_hours',
        'status',
        'credit_hours',
        'learning_outcomes',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(LecturerProfile::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course_id',
            'prerequisite_course_id'
        )->withTimestamps();
    }

    public function learningActivities(): HasMany
    {
        return $this->hasMany(LearningActivity::class);
    }

    public function courseMaterials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
            ->withPivot('status', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    // =====================
    // SCOPES
    // =====================

    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // =====================
    // METHODS
    // =====================

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function publish(): void
    {
        $this->update(['status' => 'published']);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }
}
