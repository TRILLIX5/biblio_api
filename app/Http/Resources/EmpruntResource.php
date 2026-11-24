<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpruntResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'livre'        => new LivreResource($this->whenLoaded('livre')),
            'membre_id'    => $this->membre_id,
            'emprunte_le'  => $this->emprunte_le->toDateString(),   // "le xx/yy/20zz"
            'retour_prevu' => $this->retour_prevu->toDateString(),
            'rendu_le'     => $this->rendu_le?->toDateString(),     // ?-> : null si pas encore rendu
            'en_retard'    => $this->estEnRetard(),
        ];
    }
}
