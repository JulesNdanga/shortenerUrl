<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ShortUrl;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class ShortUrlController extends Controller
{
    /**
     * Crée une nouvelle URL courte
     * POST /api/shorten
     */
    public function shorten(Request $request)
    {
        // Validation de base
        $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        $originalUrl = $request->input('url');

        // Générer un code court unique
        do {
            $shortCode = Str::random(6);
        } while (ShortUrl::where('short_code', $shortCode)->exists());

        // Sauvegarder le mapping
        $shortUrl = ShortUrl::create([
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
            'click_count' => 0,
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
     * Affiche les statistiques de base pour une URL courte
     * GET /api/stats/{short_code}
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
}

