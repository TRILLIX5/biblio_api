<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membre extends Model
{
    protected $fillable = ['nom', 'email', 'matricule'];

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class);
    }

    // Emprunts non rendus. Retourne une REQUÊTE (HasMany) qu'on peut encore compléter.
    public function empruntsEnCours(): HasMany
    {
        return $this->emprunts()->whereNull('rendu_le');
    }

    //regle du product backlog(TD): a-t-il au moins un emprunt en cours dont la date de retour est dépassée ?
    public function aUnEmpruntEnRetard(): bool
    {
        return $this->empruntsEnCours()
            ->whereDate('retour_prevu', '<', today())
            ->exists(); 
    }
}
