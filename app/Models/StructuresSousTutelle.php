<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class StructuresSousTutelle extends Model
{
     use Filterable,HasFactory;
    protected $fillable = [
        'name',
        'description',
        'logo',
        'link',
        'name_responsable',
        'structure_id'
    ];

    // Relation vers la structure parente
    public function structure()
    {
        return $this->belongsTo(Structures::class, 'structure_id');
    }
}
