<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivreResource extends JsonResource
{
   
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'titre'       => $this->titre,
            'isbn'        => $this->isbn,
            'annee'       => $this->annee,
            'exemplaires' => $this->exemplaires,
            'disponibles' => $this->disponibles,
            // whenLoaded : inclut l'auteur SEULEMENT s'il a été chargé avec with('auteur').
            // Sinon on évite une requête SQL cachée par livre (problème "N+1").
            'auteur' => new AuteurResource($this->whenLoaded('auteur')),
        ];
    }
}
