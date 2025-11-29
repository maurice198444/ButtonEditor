<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardVersionRequest;
use App\Models\Card;
use App\Services\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CardController extends Controller
{
    public function __construct(private readonly CardService $cardService)
    {
    }

    public function edit(Card $card): View
    {
        return view('cards.editor', compact('card'));
    }

    public function show(Card $card): JsonResponse
    {
        $card->load('currentVersion');

        $document = $card->currentVersion?->json ?? $this->defaultDocument($card);

        return response()->json([
            'card'     => $card,
            'document' => $document,
        ]);
    }

    public function storeVersion(StoreCardVersionRequest $request, Card $card): JsonResponse
    {
        // solange du noch keine Auth hast, nimm einfach owner_id als userId
        $userId = $request->user()?->id ?? $card->owner_id;

        $version = $this->cardService->saveVersion(
            $card,
            $request->validated('json'),
            $userId
        );

        return response()->json([
            'success' => true,
            'version' => $version->version,
        ]);
    }

    /**
     * Default-Card-Dokument, wenn noch keine Version existiert.
     */
    protected function defaultDocument(Card $card): array
    {
        return [
            'id'      => $card->id,
            'name'    => $card->name,
            'canvas'  => [
                'width'  => 600,
                'height' => 350,
                'bg'     => [
                    'color' => '#111827',
                    'image' => null,
                ],
            ],
            'elements' => [
                [
                    'id'       => 'el-1',
                    'type'     => 'icon',
                    'position' => ['x' => 50, 'y' => 50],
                    'size'     => ['width' => 80, 'height' => 80],
                    'transform'=> ['rotation' => 0, 'scale' => 1],
                    'style'    => [
                        'color'   => '#fbbf24',
                        'bg'      => null,
                        'opacity' => 1,
                    ],
                    'data'     => [
                        'icon'   => 'mdi:lightbulb',
                        'entity' => 'light.living_room',
                    ],
                ],
            ],
        ];
    }
}
