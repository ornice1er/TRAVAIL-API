<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Organigramme extends Model
{
    use HasFactory, Filterable, SoftDeletes, HasUuids;
    protected $fillable = [
        'name',
        'photo',
        'media_id',
        'legend',
    ];

    /**
     * Relation avec le média associé.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Relation avec les légendes associées à l’organigramme.
     */
    public function legendes()
    {
        return $this->hasMany(Legendes::class, 'organigramme_id');
    }
}
