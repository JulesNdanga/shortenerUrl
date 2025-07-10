<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ShortUrl;
use App\Models\Click;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class RedirectController extends Controller
{
    /**
     * Redirige vers l'URL d'origine et enregistre le clic
     * GET /{short_code}
     */
    public function redirect($short_code)
    {
        // Recherche de l'URL courte
        $shortUrl = ShortUrl::where('short_code', $short_code)->first();
        if (!$shortUrl) {
            // Code court invalide
            return response()->view('errors.404', [], 404);
        }

        // Incrémenter le compteur de clics
        $shortUrl->increment('click_count');

        // Enregistrer le clic avec horodatage
        Click::create([
            'short_url_id' => $shortUrl->id,
            'clicked_at' => Carbon::now(),
        ]);

        // Rediriger vers l'URL d'origine
        return redirect()->away($shortUrl->original_url);
    }
}

