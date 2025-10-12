<?php

namespace App\Http\Requests\Formation;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'libellé' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cloture' => 'nullable|date',
            'has_principal_access' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'cloture.date' => 'La date de clôture doit être une date valide.',
            'file.mimes' => 'Le fichier doit être de type : pdf, doc, docx, jpg, jpeg, png.',
            'file.max' => 'Le fichier ne doit pas dépasser 10MB.',
        ];
    }

    protected function prepareForValidation() {}
}