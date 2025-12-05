<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Модель Задачи
 */
class Task extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assignee_id',
    ];

    /**
     * @return BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
