<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardVersionRequest;
use App\Models\Card;
use App\Services\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CardController extends Controller
{
    public function __construct(private readonly CardService $cardService) {}

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

            // Globale Bindings für Home Assistant
            'bindings' => [
                'entity'     => 'light.wohnzimmer',
                'name'       => 'Wohnzimmer Lampe',
                'icon'       => 'mdi:lightbulb',
                'tap_action' => 'toggle', // oder 'more-info', 'navigate', ...
            ],

            // Canvas-Grundfläche
            'canvas'  => [
                'width'  => 600,
                'height' => 350,
                'bg'     => [
                    'color' => '#111827',
                    'image' => null,
                ],
            ],

            // Elemente auf dem Canvas
            'elements' => [
                // ICON
                [
                    'id'       => 'el-1',
                    'type'     => 'icon',
                    'position' => ['x' => 60, 'y' => 60],
                    'size'     => ['width' => 80, 'height' => 80],
                    'transform' => ['rotation' => 0, 'scale' => 1],
                    'style'    => [
                        'color'   => '#ff9800',
                        'opacity' => 1,
                    ],
                    'data'     => [
                        'icon'   => 'mdi:car',         // default Icon
                        'entity' => 'light.wohnzimmer',
                    ],
                    'states'   => [], // später: zustandsabhängige Styles
                ],

                // TEXT (z. B. Name der Entität)
                [
                    'id'       => 'el-2',
                    'type'     => 'text',
                    'position' => ['x' => 170, 'y' => 80],
                    'size'     => ['width' => 260, 'height' => 40],
                    'transform' => ['rotation' => 0, 'scale' => 1],
                    'style'    => [
                        'color'      => '#ffffff',
                        'fontSize'   => 18,
                        'fontWeight' => 300,
                        'textAlign'  => 'left',
                    ],
                    'data'     => [
                        'text'    => 'Wohnzimmer Lampe',
                        'binding' => 'name', // später aus bindings.name ableitbar
                    ],
                    'states'   => [],
                ],
            ],
        ];
    }
}
