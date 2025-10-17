<?php

namespace App\Http\Requests\ActualitesFile;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateActualitesFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'=>'string|required',
            'nom'=>'string|required',
            'reference'=>'string|nullable',
            'filename'=>'string|required',
            'actualites_id'=>'required|exists:actualites,id',
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
            'actualites_id.required' => 'L’actualité associée est requise.',
            'actualites_id.exists' => 'L’actualité associée est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
