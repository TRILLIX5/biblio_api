<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLivreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        
        $livre = $this->route('livre');
        
        return [
            // 'sometimes': modification PARTIELLE, on ne valide que ce qui est envoyé
            'auteur_id' => ['sometimes', 'integer', 'exists:auteurs,id'],
            'titre'     => ['sometimes', 'string', 'max:255'],
            // unique, mais en ignorant le livre qu'on est en train de modifier
            'isbn'      => ['sometimes', 'string', 'max:20', Rule::unique('livres', 'isbn')->ignore($livre)],
            'annee'     => ['sometimes', 'nullable', 'integer', 'between:1000,2100'],

            // On ne modifie ni "disponibles" ni "exemplaires" ici (ce sont des bonus qui demandent
            // de recalculer le stock). Les interdire évite de casser la cohérence.
            'disponibles' => ['prohibited'],
            'exemplaires' => ['prohibited'],
            
        ];
    }
}
