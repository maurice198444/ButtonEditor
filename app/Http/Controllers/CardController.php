<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardVersionRequest;
use App\Models\Card;
use App\Services\CardService;
use Illuminate\Http\JsonResponse;

/**
 * Class CardController
 *
 * Provides endpoints for retrieving and persisting card versions.
 */
class CardController extends Controller
{
    public function __construct(private readonly CardService $cardService)
    {
    }

    /**
     * Display the specified card with its current version JSON.
     */
    public function show(Card $card): JsonResponse
    {
        $card->load('currentVersion');
        $document = $card->currentVersion?->json ?? [
            'id' => $card->id,
            'name' => $card->name,
            'canvas' => [],
            'elements' => [],
        ];

        return response()->json([
            'card' => $card,
            'document' => $document,
        ]);
    }

    /**
     * Store a newly created card version in storage.
     */
    public function storeVersion(StoreCardVersionRequest $request, Card $card): JsonResponse
    {
        $version = $this->cardService->saveVersion($card, $request->validated('json'), $request->user()->id);

        return response()->json([
            'success' => true,
            'version' => $version,
        ]);
    }
}
