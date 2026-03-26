<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'criteria_name',
        'max_points',
        'description',
        'seq',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }
}
