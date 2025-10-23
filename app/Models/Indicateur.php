<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicateur extends Model
{
    use HasFactory, Filterable;

    protected $table = 'indicateurs';

    // Liste blanche des attributs pouvant être filtrés
    private static $whiteListFilter = ['*'];

    protected $fillable = [
        'libelle',
        'valeur',
        'structure_id',
    ];

    /**
     * Relation avec le modèle Structure
     */
    public function structure()
    {
        return $this->belongsTo(Structures::class, 'structure_id');
    }
}