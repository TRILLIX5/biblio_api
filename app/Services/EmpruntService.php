<?php

namespace App\Services;

use App\Exceptions\RegleMetierException;
use App\Models\Emprunt;
use App\Models\Livre;
use App\Models\Membre;
use Illuminate\Support\Facades\DB;

class EmpruntService
{
    public const DUREE_EMPRUNT_JOURS = 14; // la règle 5 , abb 14 fan ley duré
    public const LIMITE_EMPRUNTS = 3;      // la règle 3 , 3 abb en cours kessei nga meune am

    /* Crée un emprunt en respectant les règles 2, 3 et 4.Toute violation lance une RegleMetierException (409).*/
    public function emprunter(int $livreId, int $membreId): Emprunt
    {
        // Tout ce qui est dans la transaction réussit ensemble ou échoue ensemble
        return DB::transaction(function () use ($livreId, $membreId) {

            // je verrouille avec la fonction lockForUpdate pour verifier les emprunts simultanés du membres
            $membre = Membre::lockForUpdate()->findOrFail($membreId);

            // Règle 4 : un emprunt en retard bloque tout nouvel emprunt (del dello)
            if ($membre->aUnEmpruntEnRetard()) {
                throw new RegleMetierException(
                    'membre_en_retard',
                    'Ce membre a un emprunt en retard et ne peut plus emprunter.'
                );
            }

            // Règle 3 : maximum 3 emprunts en cours
            if ($membre->empruntsEnCours()->count() >= self::LIMITE_EMPRUNTS) {
                throw new RegleMetierException(
                    'limite_atteinte',
                    'Limite de ' . self::LIMITE_EMPRUNTS . ' emprunts en cours atteinte.'
                );
            }

            // Règle 2 : il doit rester un exemplaire
            // decrement() renvoie le nombre de lignes modifiées
            $reserve = Livre::whereKey($livreId)
                ->where('disponibles', '>', 0)
                ->decrement('disponibles');

            if ($reserve === 0) {
                throw new RegleMetierException(
                    'livre_indisponible',
                    'Aucun exemplaire de ce livre n\'est disponible.'
                );
            }

            // Règle 5 : durée de 14 jours
            $emprunt = Emprunt::create([
                'livre_id'     => $livreId,
                'membre_id'    => $membreId,
                'emprunte_le'  => today(),
                'retour_prevu' => today()->addDays(self::DUREE_EMPRUNT_JOURS),
            ]);

            return $emprunt->load('livre.auteur'); // charge le livre et l'auteur du livre
        });
    }

    /*Rend un livre Règle 5 : on ne peut rendre qu'une seule fois*/
    public function rendre(Emprunt $emprunt): Emprunt
    {
        return DB::transaction(function () use ($emprunt) {

            // "marque comme rendu SEULEMENT si pas déjà rendu".
            // Si deux requêtes arrivent en même temps, une seule modifiera 1 ligne.
            $modifies = Emprunt::whereKey($emprunt->id)
                ->whereNull('rendu_le')
                ->update(['rendu_le' => today()->toDateString()]);

            if ($modifies === 0) {
                throw new RegleMetierException('deja_rendu', 'Cet emprunt a déjà été rendu.');
            }

            // On remet l'exemplaire en stock
            Livre::whereKey($emprunt->livre_id)->increment('disponibles');

            return $emprunt->refresh()->load('livre.auteur'); // refresh() relit la ligne en base
        });
    }
}