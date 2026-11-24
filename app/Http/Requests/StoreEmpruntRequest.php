<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmpruntRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'livre_id'  => ['required', 'integer', 'exists:livres,id'],
            'membre_id' => ['required', 'integer', 'exists:membres,id'],
        ];
    }
}
