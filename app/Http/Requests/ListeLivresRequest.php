<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListeLivresRequest extends FormRequest
{
    //puisq'il n'ya pas d'authentification tout le monde est autorisé
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'q'          => ['sometimes', 'string', 'max:100'], // sometimes signifie:validé seulement s'il est présent
            'disponible' => ['sometimes', 'boolean'],           // accepte 1, 0, true, false, "1", "0"
            'page'       => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
