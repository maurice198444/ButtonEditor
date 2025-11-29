<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CardVersion
 *
 * Represents a specific version of a card document.
 */
class CardVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_id',
        'version',
        'json',
        'preview_thumb',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'json' => 'array',
    ];

    /**
     * Get the card that owns this version.
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
