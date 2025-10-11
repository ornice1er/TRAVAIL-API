<?php

namespace App\Http\Requests\Recrutement;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRecrutementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'exigences' => 'nullable|string',
            'avantages' => 'nullable|string',
            'responsabilites' => 'nullable|string',
            'type_contrat' => 'required|string|max:100',
            'lieu_travail' => 'required|string|max:255',
            'salaire_min' => 'nullable|numeric|min:0',
            'salaire_max' => 'nullable|numeric|min:0|gte:salaire_min',
            'date_limite' => 'required|date|after:today',
            'experience_requise' => 'nullable|string|max:255',
            'niveau_etude' => 'nullable|string|max:255',
            'structure_id' => 'required|exists:structures,id',
            'status' => 'sometimes|integer|in:0,1,2,3,4,5',
            'fichier' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'type_contrat.required' => 'Le type de contrat est obligatoire.',
            'lieu_travail.required' => 'Le lieu de travail est obligatoire.',
            'date_limite.required' => 'La date limite est obligatoire.',
            'date_limite.after' => 'La date limite doit être supérieure à aujourd\'hui.',
            'salaire_max.gte' => 'Le salaire maximum doit être supérieur ou égal au salaire minimum.',
            'structure_id.required' => 'La structure est obligatoire.',
            'structure_id.exists' => 'La structure sélectionnée n\'existe pas.',
            'fichier.mimes' => 'Le fichier doit être de type PDF, DOC ou DOCX.',
            'fichier.max' => 'Le fichier ne peut pas dépasser 2 Mo.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être de type JPEG, PNG, JPG ou GIF.',
            'image.max' => 'L\'image ne peut pas dépasser 2 Mo.',
        ];
    }

    protected function prepareForValidation() {}
}
