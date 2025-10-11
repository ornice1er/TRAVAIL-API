<?php

namespace App\Http\Repositories;

use App\Models\Actualite;
use App\Models\ActualitesFiles;
use App\Models\Actualites;
use App\Models\Communique;
use App\Models\Communiques;
use App\Models\Media;
use App\Models\Mot;
use App\Models\Mots;
use App\Models\Structure;
use App\Models\Structures;
use App\Models\TypesStructure;
use App\Traits\Repository;

class PageRepository
{
    use Repository;

    /**
     * Récupère les fichiers d'une galerie par type.
     */
    public function getGalleryFiles($type)
    {
        return ActualitesFiles::where('type', $type)->get();
    }

    /**
     * Récupère un communiqué par slug.
     */
    public function getCommuniqueBySlug($slug)
    {
        return Communique::whereSlug($slug)->first();
    }

    /**
     * Récupère une actualité par slug.
     */
    public function getActualiteBySlug($slug)
    {
        return Actualite::whereSlug($slug)->first();
    }

    /**
     * Récupère le mot du ministre.
     */
    public function getMinistreWord()
    {
        $type = TypesStructure::where('is_parent', true)->first();
        $structure = Structure::where('type_structure_id', $type->id)->first();
        $word = Mot::where('structure_id', $structure->id)->get()->last();

        return $word?->resume;
    }

    /**
     * Récupère les derniers communiqués.
     */
    public function getLastCommuniques()
    {
        return Media::with('communique')
            ->where('type', 'communique')
            ->where('is_published', true)
            ->where('is_archived', false)
            ->where('has_principal_access', true)
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();
    }
}