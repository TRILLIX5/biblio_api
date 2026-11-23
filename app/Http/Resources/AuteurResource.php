<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuteurResource extends JsonResource
{
    //$this->klksoze permet d'acceder aux attributs du modèle enveloppé
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'nom'  => $this->nom,
            'pays' => $this->pays,
        ];
    }
}
