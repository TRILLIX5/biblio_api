<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auteur extends Model
{
    // Champs que l'on a le droit de remplir via create() / update()
    protected $fillable = ['nom', 'pays'];

    // Un auteur a plusieurs livres
    public function livres(): HasMany
    {
        return $this->hasMany(Livre::class);
    }
}
