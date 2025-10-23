<?php

namespace App\Http\Requests\FicheMetier;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFicheMetierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => 'required|required|string|max:255',
            'resume' => 'required|required|string|max:500',
            'description' => 'required|required|string',
            'structure_id' => 'required|required|exists:structures,id',
            'thematique' => 'required|required|array',
            'thematique.*' => 'string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            
            'resume.required' => 'Le résumé est obligatoire.',
            'resume.string' => 'Le résumé doit être une chaîne de caractères.',
            'resume.max' => 'Le résumé ne peut pas dépasser 500 caractères.',
            
            'description.required' => 'La description est obligatoire.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            
            'structure_id.required' => 'La structure est obligatoire.',
            'structure_id.exists' => 'La structure sélectionnée n\'existe pas.',
            
            'thematique.required' => 'Les thématiques sont obligatoires.',
            'thematique.array' => 'Les thématiques doivent être un tableau.',
            'thematique.*.string' => 'Chaque thématique doit être une chaîne de caractères.',
            'thematique.*.max' => 'Chaque thématique ne peut pas dépasser 255 caractères.',
        ];
    }
}