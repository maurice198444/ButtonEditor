<?php

namespace App\Services;

use App\Models\Card;
use App\Models\CardVersion;
use Illuminate\Support\Facades\DB;

/**
 * Class CardService
 *
 * Handles business logic around card versioning.
 */
class CardService
{
    /**
     * Persist a new card version and update the card's current version reference.
     */
    public function saveVersion(Card $card, array $payload, int $userId): CardVersion
    {
        return DB::transaction(function () use ($card, $payload, $userId) {
            $currentVersion = $card->versions()->max('version') ?? 0;

            $cardVersion = CardVersion::create([
                'card_id' => $card->id,
                'version' => $currentVersion + 1,
                'json' => $payload,
                'preview_thumb' => $payload['preview_thumb'] ?? null,
                'created_by' => $userId,
            ]);

            $card->current_version_id = $cardVersion->id;
            $card->save();

            return $cardVersion;
        });
    }
}
