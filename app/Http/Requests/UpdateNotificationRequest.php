<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
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
            'type' => 'sometimes|required|string|max:255',
            'notifiable_type' => 'sometimes|required|string|max:255',
            'notifiable_id' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'sent_to' => 'sometimes|required|exists:users,id',
            'lu_à' => 'nullable|date',
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
            'lu_à.date' => 'La date de lecture doit être une date valide.',
        ];
    }
}