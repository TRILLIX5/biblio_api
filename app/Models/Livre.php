<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livre extends Model
{
      // disponibles est fillable car c'est NOTRE code qui le remplit.
    protected $fillable = ['auteur_id', 'titre', 'isbn', 'annee', 'exemplaires', 'disponibles'];

    // Un livre appartient à un auteur (colonne auteur_id)
    public function auteur(): BelongsTo
    {
        return $this->belongsTo(Auteur::class);
    }

    // Un livre a plusieurs emprunts
    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class);
    }
}
