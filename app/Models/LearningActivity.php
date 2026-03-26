<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'sequence',
        'is_active',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function contentActivity(): HasOne
    {
        return $this->hasOne(ContentActivity::class);
    }

    public function genericActivity(): HasOne
    {
        return $this->hasOne(GenericActivity::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            LearningActivity::class,
            'activity_prerequisites',
            'activity_id',
            'prerequisite_activity_id'
        )->withTimestamps();
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(StudentActivityTracking::class);
    }
}
