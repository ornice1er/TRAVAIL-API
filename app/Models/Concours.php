<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Concours extends Model
{
    use Filterable, HasFactory, SoftDeletes, HasUuids;
    protected $fillable = [
        'libelle',           // Attention : sans accent dans le code
        'slug',
        'statut',
        'annonce',
        'date_composition',
        'centre_composition',
        'liste_valides',
        'liste_rejetes',
        'resultat',
        'media_id',
    ];
}