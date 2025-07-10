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
    ];

    /**
     * Relation : une URL courte possède plusieurs clics
     */
    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}

