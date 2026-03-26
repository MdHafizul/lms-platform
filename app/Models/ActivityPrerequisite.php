<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityPrerequisite extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'prerequisite_activity_id',
    ];

    public $timestamps = true;

    public function activity(): BelongsTo
    {
        return $this->belongsTo(LearningActivity::class);
    }

    public function prerequisiteActivity(): BelongsTo
    {
        return $this->belongsTo(LearningActivity::class, 'prerequisite_activity_id');
    }
}
