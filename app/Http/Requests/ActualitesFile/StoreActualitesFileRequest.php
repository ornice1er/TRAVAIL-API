<?php

namespace App\Http\Requests\ActualitesFile;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreActualitesFileRequest extends FormRequest
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
            'type.required' => 'Le type de fichier est requis.',
            'nom.required' => 'Le nom du fichier est requis.',
            'reference.required' => 'La référence est obligatoire.',
            'filename.required' => 'Le fichier est requis.',
            'filename.file' => 'Le champ doit contenir un fichier valide.',
            'filename.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
            'actualites_id.required' => 'Une actualité doit être associée au fichier.',
            'actualites_id.exists' => 'L’actualité associée est introuvable.',
            ];
    }

    protected function prepareForValidation() {}
}
