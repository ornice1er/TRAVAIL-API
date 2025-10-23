<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsletterRequest extends FormRequest
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
            'titre' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('newsletters', 'titre')->ignore($this->newsletter)
            ],
            'structure_id' => 'required|exists:structures,id',
            'status' => 'nullable|in:actif,inactif,archivé',
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
            'titre.required' => 'L\'adresse email est obligatoire.',
            'titre.email' => 'L\'adresse email doit être valide.',
            'titre.unique' => 'Cette adresse email est déjà inscrite à la newsletter.',
            'structure_id.required' => 'La structure est obligatoire.',
            'structure_id.exists' => 'La structure sélectionnée n\'existe pas.',
            'status.in' => 'Le statut doit être actif, inactif ou archivé.',
        ];
    }
}