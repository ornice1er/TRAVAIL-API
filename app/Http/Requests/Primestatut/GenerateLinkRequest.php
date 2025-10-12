<?php

namespace App\Http\Requests\Primestatut;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GenerateLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'lieu' => 'nullable|string|max:255',
            'type_Primestatut' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'lieu.string' => 'Le lieu doit être une chaîne de caractères.',
            'lieu.max' => 'Le lieu ne peut pas dépasser 255 caractères.',
            'type_Primestatut.string' => 'Le type doit être une chaîne de caractères.',
            'type_Primestatut.max' => 'Le type ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.'
        ];
    }

    protected function prepareForValidation() {}
}