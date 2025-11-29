<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Card
 *
 * Represents a design card that can hold multiple versions.
 */
class Card extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'current_version_id',
    ];

    /**
     * Get all versions for the card.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(CardVersion::class);
    }

    /**
     * Get the card's current version.
     */
    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(CardVersion::class, 'current_version_id');
    }
}
