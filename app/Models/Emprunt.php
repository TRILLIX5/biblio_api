<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emprunt extends Model
{
    protected $fillable = ['livre_id', 'membre_id', 'emprunte_le', 'retour_prevu', 'rendu_le'];

    // Grâce à 'date', $emprunt->retour_prevu est un objet Carbon (et non une chaîne).
    protected function casts(): array
    {
        return [
            'emprunte_le'  => 'date',
            'retour_prevu' => 'date',
            'rendu_le'     => 'date',
        ];
    }

    public function livre(): BelongsTo
    {
        return $this->belongsTo(Livre::class);
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }

    // Un emprunt est en retard s'il n'est pas rendu ET que la date prévue est strictement passée
    public function estEnRetard(): bool
    {
        return $this->rendu_le === null && $this->retour_prevu->lt(today());
    }
}
