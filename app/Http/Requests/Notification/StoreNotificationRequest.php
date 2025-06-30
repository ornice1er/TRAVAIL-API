<?php

namespace App\Http\Requests\Notification;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|max:191',
            'notifiable_type' => 'required|string|max:191',
            'notifiable_id' => 'required|integer',
            'description' => 'required|string',
            'lu_à' => 'nullable|date',
            'sent_to' => 'nullable|exists:users,id',
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
            'notifiable_type.required' => 'Le type notifiable est requis.',
            'notifiable_id.required' => 'L\'ID notifiable est requis.',
            'description.required' => 'La description est requise.',
            'lu_à.date' => 'Le champ "lu à" doit être une date valide.',
            'sent_to.exists' => 'L\'utilisateur destinataire est introuvable.',
        ];
    }

    protected function prepareForValidation() {}
}
