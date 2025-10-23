<?php

namespace App\Http\Requests\Indicateur;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreIndicateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle' => 'required|string|max:255',
            'valeur' => 'required|string|max:255',
            'structure_id' => 'required|exists:structures,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            
            'valeur.required' => 'La valeur est obligatoire.',
            'valeur.string' => 'La valeur doit être une chaîne de caractères.',
            'valeur.max' => 'La valeur ne peut pas dépasser 255 caractères.',
            
            'structure_id.required' => 'La structure est obligatoire.',
            'structure_id.exists' => 'La structure sélectionnée n\'existe pas.',
        ];
    }
}