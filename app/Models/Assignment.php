<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_activity_id',
        'due_date',
        'max_submissions',
        'instructions',
        'submission_type',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'settings' => 'json',
        ];
    }

    public function learningActivity(): BelongsTo
    {
        return $this->belongsTo(LearningActivity::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function gradingSchemes(): HasMany
    {
        return $this->hasMany(GradingScheme::class);
    }
}
