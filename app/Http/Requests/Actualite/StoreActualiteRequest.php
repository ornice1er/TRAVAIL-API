<?php

namespace App\Http\Requests\Actualite;

use App\Utilities\Common;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreActualiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'structure_id'           => ['required', 'exists:structures,id'],
            'category_id'            => ['required', 'exists:categories,id'],
            'title'                  => ['required', 'string', 'max:255'],
            'sub_description'        => ['required', 'string'],
            'description'            => ['required', 'string'],
            'link'                   => ['nullable', 'url'],
            'author'                 => ['nullable', 'string', 'max:255'],
            'photo'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5048'],
            'big_photo'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5048'],
            'has_principal_access'   => ['required', 'in:0,1'],
        ];
    }

    public function messages()
    {
        return [
            'structure_id.required'      => 'Veuillez sélectionner une structure.',
            'structure_id.exists'        => 'La structure sélectionnée est invalide.',

            'category_id.required'       => 'Veuillez sélectionner une catégorie.',
            'category_id.exists'         => 'La catégorie sélectionnée est invalide.',

            'title.required'             => 'Le titre est obligatoire.',
            'title.max'                  => 'Le titre est trop long.',

            'sub_description.required'   => 'Le résumé est obligatoire.',

            'description.required'       => 'La description est obligatoire.',

            'link.url'                   => 'Le lien doit être une URL valide.',

            'author.required'            => "Le nom de l'auteur est obligatoire.",
            'author.max'                 => "Le nom de l'auteur est trop long.",

            'photo.image'                => "La photo doit être une image valide.",
            'photo.mimes'                => "Formats autorisés : JPG, JPEG, PNG, WEBP.",
            'photo.max'                  => "La photo ne doit pas dépasser 5 Mo.",

            'big_photo.image'            => "La photo miniature doit être une image valide.",
            'big_photo.mimes'            => "Formats autorisés : JPG, JPEG, PNG, WEBP.",
            'big_photo.max'              => "La photo miniature ne doit pas dépasser 5 Mo.",

            'has_principal_access.required' => 'Veuillez indiquer si l’actualité est publiée sur l’espace principal.',
            'has_principal_access.in'       => 'La valeur choisie est invalide.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Common::error($validator->errors()->first(), $validator->errors()));
    }


    protected function prepareForValidation() {}
}
