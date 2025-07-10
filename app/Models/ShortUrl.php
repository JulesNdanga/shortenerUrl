<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    // Nom de la table associée
    protected $table = 'short_urls';

    // Attributs modifiables en masse
    protected $fillable = [
        'original_url',
        'short_code',
        'click_count',
        'expires_at', // Date d'expiration optionnelle
        'user_id', // Propriétaire de l'URL
    ];

    /**
     * Relation : une URL courte possède plusieurs clics
     */
    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    /**
     * Propriétaire de l'URL courte
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

