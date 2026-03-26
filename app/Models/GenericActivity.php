<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenericActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_activity_id',
        'instructions',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'configuration' => 'json',
        ];
    }

    public function learningActivity(): BelongsTo
    {
        return $this->belongsTo(LearningActivity::class);
    }
}
