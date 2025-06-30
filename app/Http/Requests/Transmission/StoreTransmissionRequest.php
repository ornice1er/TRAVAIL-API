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
            'is_last' => 'required|boolean',
            'media_id' => 'required|exists:media,id',
            'from' => 'required|exists:users,id',
            'to' => 'required|exists:users,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }

    public function messages(): array
    {
        return [
             'is_last.required' => 'Le champ is_last est requis.',
            'is_last.boolean' => 'Le champ is_last doit être un booléen.',
            'media_id.required' => 'L\'identifiant du média est requis.',
            'media_id.exists' => 'Le média spécifié est introuvable.',
            'from.required' => 'Le champ "from" est requis.',
            'from.exists' => 'L\'utilisateur source est introuvable.',
            'to.required' => 'Le champ "to" est requis.',
            'to.exists' => 'L\'utilisateur destinataire est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
