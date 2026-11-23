<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class RegleMetierException extends Exception
//Cette classe est lancée quand une regle de gestion est violée
{
        public function __construct(
        public readonly string $codeMetier,   // exemple:limite_atteinte
        string $message,                      //Limite de 3 emprunts en cours atteinte
        public readonly int $statut = 409,  
    ) {
        parent::__construct($message);
    }

    // la fonction render() est appelée quand on a cette exception remonte :
    // c'est ici qu'on code la réponse JSON au format imposé par le sujet.
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'code'    => $this->codeMetier,
        ], $this->statut);
    }

}
