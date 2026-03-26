<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id',
        'title',
        'description',
        'session_date',
        'start_time',
        'end_time',
        'location',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'datetime',
            'metadata' => 'json',
        ];
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }
}
