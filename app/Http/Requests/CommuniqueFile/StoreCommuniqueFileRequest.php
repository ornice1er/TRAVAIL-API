<?php

namespace App\Http\Requests\CommuniqueFile;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCommuniqueFileRequest extends FormRequest
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
            'communiques_id'=>'required|exists:communiques,id',

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
            'communiques_id.required' => 'Un communiqué doit être associé au fichier.',
            'communiques_id.exists' => 'Le communiqué associé est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
