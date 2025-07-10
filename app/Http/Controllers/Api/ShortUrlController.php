<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel URL Shortener API",
 *     description="Documentation interactive de l'API du raccourcisseur d'URL Laravel avec authentification Sanctum, gestion des liens, batch, historique, etc.",
 *     @OA\Contact(
 *         email="contact@example.com"
 *     )
 * )
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ShortUrl;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class ShortUrlController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/shorten",
     *     summary="Crée une nouvelle URL courte",
     *     tags={"ShortUrl"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(property="url", type="string", example="https://google.com"),
     *             @OA\Property(property="custom_code", type="string", example="moncode"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2025-12-31T23:59:59")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL raccourcie créée",
     *         @OA\JsonContent(
     *             @OA\Property(property="short_url", type="string"),
     *             @OA\Property(property="original_url", type="string"),
     *             @OA\Property(property="short_code", type="string")
     *         )
     *     )
     * )
     */
    public function shorten(Request $request)
    {
        // Validation de base
        $request->validate([
            'url' => 'required|url|max:2048',
            'custom_code' => 'nullable|alpha_num|min:4|max:32', // code court personnalisé optionnel
        ]);

        $originalUrl = $request->input('url');
        $customCode = $request->input('custom_code');

        if ($customCode) {
            // Vérifier unicité du code personnalisé
            $existing = ShortUrl::where('short_code', $customCode)->first();
            if ($existing) {
                // Retourner le mapping existant avec un message explicite
                return response()->json([
                    'message' => 'Ce code court personnalisé existe déjà.',
                    'short_url' => URL::to('/') . '/' . $existing->short_code,
                    'original_url' => $existing->original_url,
                    'short_code' => $existing->short_code,
                ], 200);
            }
            $shortCode = $customCode;
        } else {
            // Générer un code court unique aléatoire
            do {
                $shortCode = Str::random(6);
            } while (ShortUrl::where('short_code', $shortCode)->exists());
        }

        // Sauvegarder le mapping
        $shortUrl = ShortUrl::create([
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
            'click_count' => 0,
            'expires_at' => $request->input('expires_at'), // Peut être null
            'user_id' => $request->user()->id,
        ]);

        // Construire l'URL courte complète
        $shortUrlFull = URL::to('/') . '/' . $shortCode;

        // Réponse JSON
        return response()->json([
            'short_url' => $shortUrlFull,
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/stats/{short_code}",
     *     summary="Statistiques d'une URL courte",
     *     tags={"ShortUrl"},
     *     @OA\Parameter(
     *         name="short_code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques de l'URL courte",
     *         @OA\JsonContent(
     *             @OA\Property(property="original_url", type="string"),
     *             @OA\Property(property="short_code", type="string"),
     *             @OA\Property(property="click_count", type="integer"),
     *             @OA\Property(property="created_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=404, description="URL courte introuvable")
     * )
     */
    public function stats($short_code)
    {
        $shortUrl = ShortUrl::where('short_code', $short_code)->first();
        if (!$shortUrl) {
            return response()->json(['error' => 'URL courte introuvable'], 404);
        }

        return response()->json([
            'original_url' => $shortUrl->original_url,
            'short_code' => $shortUrl->short_code,
            'click_count' => $shortUrl->click_count,
            'created_at' => $shortUrl->created_at,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/shorten/batch",
     *     summary="Raccourcissement d'URL en masse",
     *     tags={"ShortUrl"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="url", type="string", example="https://google.com"),
     *                     @OA\Property(property="custom_code", type="string", example="codeperso"),
     *                     @OA\Property(property="expires_at", type="string", format="date-time", example="2025-12-31T23:59:59")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats du batch",
     *         @OA\JsonContent(
     *             @OA\Property(property="results", type="array", @OA\Items(
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="short_url", type="string"),
     *                 @OA\Property(property="original_url", type="string"),
     *                 @OA\Property(property="short_code", type="string"),
     *                 @OA\Property(property="message", type="string")
     *             ))
     *         )
     *     )
     * )
     */
    public function shortenBatch(Request $request)
    {
        // Validation du tableau d'URLs
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.url' => 'required|url|max:2048',
            'items.*.custom_code' => 'nullable|alpha_num|min:4|max:32',
            'items.*.expires_at' => 'nullable|date',
        ]);

        $results = [];
        foreach ($request->input('items') as $item) {
            $originalUrl = $item['url'];
            $customCode = $item['custom_code'] ?? null;
            $expiresAt = $item['expires_at'] ?? null;

            try {
                if ($customCode) {
                    $existing = ShortUrl::where('short_code', $customCode)->first();
                    if ($existing) {
                        $results[] = [
                            'status' => 'exists',
                            'message' => 'Ce code court personnalisé existe déjà.',
                            'short_url' => URL::to('/') . '/' . $existing->short_code,
                            'original_url' => $existing->original_url,
                            'short_code' => $existing->short_code,
                        ];
                        continue;
                    }
                    $shortCode = $customCode;
                } else {
                    do {
                        $shortCode = Str::random(6);
                    } while (ShortUrl::where('short_code', $shortCode)->exists());
                }

                $shortUrl = ShortUrl::create([
                    'original_url' => $originalUrl,
                    'short_code' => $shortCode,
                    'click_count' => 0,
                    'expires_at' => $expiresAt,
                    'user_id' => $request->user()->id,
                ]);

                $results[] = [
                    'status' => 'created',
                    'short_url' => URL::to('/') . '/' . $shortCode,
                    'original_url' => $originalUrl,
                    'short_code' => $shortCode,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'status' => 'error',
                    'original_url' => $originalUrl,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json(['results' => $results]);
    }

    /**
     * @OA\Get(
     *     path="/api/history",
     *     summary="Historique des liens de l'utilisateur connecté",
     *     tags={"ShortUrl"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des liens",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="short_code", type="string"),
     *             @OA\Property(property="original_url", type="string"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="click_count", type="integer"),
     *             @OA\Property(property="expires_at", type="string", format="date-time")
     *         ))
     *     )
     * )
     */
    public function history()
    {
        $urls = \App\Models\ShortUrl::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get(['short_code', 'original_url', 'created_at', 'click_count', 'expires_at']);
        return response()->json($urls);
    }
}

