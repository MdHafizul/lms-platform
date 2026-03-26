<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_activity_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'order',
    ];

    public function contentActivity(): BelongsTo
    {
        return $this->belongsTo(ContentActivity::class);
    }
}
