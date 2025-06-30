<?php

namespace App\Http\Requests\Galerie;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreGalerieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'photo' => 'required|string|max:191',
            'description' => 'nullable|string|max:255',
            'actualite_id' => 'nullable|exists:actualites,id',
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
            'photo.required' => 'La photo est requise.',
            'actualite_id.exists' => "L'actualité spécifiée est introuvable.",
        ];
    }

    protected function prepareForValidation() {}
}
