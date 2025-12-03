<?php

namespace App\Http\Requests\CommuniquesFile;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCommuniquesFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string',
              'reference' => 'nullable|string',
            'description' => 'nullable|string',
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
            'nom.required' => 'Le titre est requis.',
            'file.mimes' => 'Le fichier doit être de type : pdf, doc, docx, jpg, jpeg, png.',
            'file.max' => 'Le fichier ne doit pas dépasser 10MB.',
        ];
    }

    protected function prepareForValidation() {}
}