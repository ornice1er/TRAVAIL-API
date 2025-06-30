<?php

namespace App\Http\Requests\StructuresSousTutelle;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStructuresSousTutelleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
             'name' => 'required|string|max:191',
            'name_responsable' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:191',
            'link' => 'nullable|string|max:191',
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
           'name.required' => 'Le nom est requis.',
            'name_responsable.required' => 'Le nom du responsable est requis.',
            'structure_id.required' => 'La structure associée est requise.',
            'structure_id.exists' => 'La structure spécifiée est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
