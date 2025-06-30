<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class AppelsOffreFile extends Model
{
   use HasFactory;

    protected $fillable = [
        'is_result_file',
        'type',
        'nom',
        'reference',
        'filename',
        'appels_offres_id',
    ];

    // Relation inverse vers AppelsOffre (un fichier appartient à un appel d'offre)
    public function appelsOffre()
    {
        return $this->belongsTo(AppelsOffre::class, 'appels_offres_id');
    }
}
