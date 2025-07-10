<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class PreviewController extends Controller
{
    /**
     * Affiche une page de prévisualisation et vérifie la sécurité de l'URL cible.
     * GET /preview/{short_code}
     */
    public function preview($short_code)
    {
        $shortUrl = ShortUrl::where('short_code', $short_code)->first();
        if (!$shortUrl) {
            return response()->view('errors.404', [], 404);
        }

        // Vérifier expiration
        if ($shortUrl->expires_at && Carbon::now()->greaterThan($shortUrl->expires_at)) {
            return response()->view('errors.expired', ['short_code' => $short_code], 410);
        }

        $originalUrl = $shortUrl->original_url;
        $security = [
            'is_https' => str_starts_with($originalUrl, 'https://'),
            'is_blacklisted' => false,
            'http_status' => null,
            'error' => null,
        ];

        // Vérification basique blacklist (à étendre selon besoins)
        $blacklist = ['malware', 'phishing', 'adult'];
        foreach ($blacklist as $word) {
            if (stripos($originalUrl, $word) !== false) {
                $security['is_blacklisted'] = true;
            }
        }

        // Vérification du statut HTTP (timeout court)
        try {
            $resp = Http::timeout(3)->head($originalUrl);
            $security['http_status'] = $resp->status();
        } catch (\Exception $e) {
            $security['error'] = $e->getMessage();
        }

        return view('preview', [
            'short_code' => $short_code,
            'original_url' => $originalUrl,
            'security' => $security,
        ]);
    }
}
