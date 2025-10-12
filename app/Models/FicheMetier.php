<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FicheMetier extends Model
{
    use HasFactory, Filterable;

    protected $table = 'fiche_metiers';

    // Liste blanche des attributs pouvant être filtrés
    private static $whiteListFilter = ['*'];

    protected $fillable = [
        'titre',
        'resume',
        'description',
        'structure_id',
        'thematique',
    ];

    protected $casts = [
        'thematique' => 'array', // Cast automatique du JSON en array
    ];

    /**
     * Relation avec le modèle Structure
     */
    public function structure()
    {
        return $this->belongsTo(Structures::class, 'structure_id');
    }
}