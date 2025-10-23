<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'type' => 'required|string|max:255',
            'notifiable_type' => 'required|string|max:255',
            'notifiable_id' => 'required|string',
            'description' => 'required|string',
            'sent_to' => 'required|exists:users,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'type.required' => 'Le type de notification est obligatoire.',
            'notifiable_type.required' => 'Le type de l\'entité notifiable est obligatoire.',
            'notifiable_id.required' => 'L\'ID de l\'entité notifiable est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'sent_to.required' => 'Le destinataire est obligatoire.',
            'sent_to.exists' => 'Le destinataire sélectionné n\'existe pas.',
        ];
    }
}