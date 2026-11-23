<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLivreRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'auteur_id'   => ['required', 'integer', 'exists:auteurs,id'], // l'auteur doit exister
            'titre'       => ['required', 'string', 'max:255'],
            'isbn'        => ['required', 'string', 'max:20', 'unique:livres,isbn'], // pas de doublon
            'annee'       => ['nullable', 'integer', 'between:1000,2100'],
            'exemplaires' => ['required', 'integer', 'min:1'],

            // C'est la règle 5 du cahier de charges : le client ne peut PAS envoyer "disponibles".
            // 'prohibited' => s'il est présent dans la requête, c'est un 422.
            'disponibles' => ['prohibited'],
        ];
    }
}
