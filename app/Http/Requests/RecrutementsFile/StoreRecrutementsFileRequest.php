<?php

namespace App\Http\Requests\RecrutementsFile;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRecrutementsFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_result_file' => 'nullable|boolean',
            'type' => 'required|string|max:191',
            'nom' => 'required|string|max:191',
            'reference' => 'required|string|max:191',
            'filename' => 'required|string',
            'recrutement_id' => 'required|exists:recrutements,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type est requis.',
            'nom.required' => 'Le nom est requis.',
            'reference.required' => 'La référence est requise.',
            'filename.required' => 'Le fichier est requis.',
            'recrutement_id.required' => 'Le recrutement associé est requis.',
            'recrutement_id.exists' => 'Le recrutement spécifié est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
