<?php

namespace App\Http\Requests\Structure;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'name' => 'required|string|max:191',
            'job' => 'nullable|string',
            'acronym' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:structures,slug',
            'name_responsable' => 'nullable|string|max:191',
            'photo_responsable' => 'nullable|string|max:191',
            'biographie_responsable' => 'nullable|string',
            'photo' => 'nullable|string|max:191',
            'phone' => 'nullable|string|max:191',
            'email' => 'required|email|max:191',
            'vision' => 'nullable|string',
            'type_structure_id' => 'required|exists:types_structures,id',
            'fonction' => 'nullable|string|max:191',
            'responsable_text' => 'nullable|string',
            'historique' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis.',
            'acronym.required' => 'L\'acronyme est requis.',
            'slug.required' => 'Le slug est requis.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être valide.',
            'type_structure_id.required' => 'Le type de structure est requis.',
            'type_structure_id.exists' => 'Le type de structure spécifié est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
