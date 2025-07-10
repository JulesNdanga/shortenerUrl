<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    // Nom de la table associée
    protected $table = 'clicks';

    // Attributs modifiables en masse
    protected $fillable = [
        'short_url_id',
        'clicked_at',
    ];

    /**
     * Relation : ce clic appartient à une URL courte
     */
    public function shortUrl()
    {
        return $this->belongsTo(ShortUrl::class);
    }
}

