<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Media extends Model
{
    use HasFactory, Filterable, SoftDeletes, HasUuids;

    protected $fillable = [
        'code',
        'is_published',
        'has_principal_access',
        'is_archived',
        'structure_id',
        'motif',
        'type',
    ];

    public function parcours()
    {
        return $this->hasMany(Parcours::class, 'media_id');
    }

    public function transmissions()
    {
        return $this->hasMany(Transmission::class, 'media_id');
    }

    public function communique()
    {
        return $this->hasOne(Communiques::class, 'media_id');
    }

    public function actualite()
    {
        return $this->hasOne(Actualites::class, 'media_id');
    }

    public function prestation()
    {
        return $this->hasOne(Prestations::class, 'media_id');
    }

    public function offre()
    {
        return $this->hasOne(AppelsOffre::class, 'media_id');
    }

    public function doc()
    {
        return $this->hasOne(Docs::class, 'media_id');
    }

    public function docdecrets()
    {
        return $this->hasOne(Docs::class, 'media_id')->where('type', 'decrets');
    }

    public function org()
    {
        return $this->hasOne(Organigrammes::class, 'media_id');
    }

    public function aof()
    {
        return $this->hasOne(AOF::class, 'media_id');
    }

    public function stage()
    {
        return $this->hasOne(Stage::class, 'media_id');
    }

    public function structure()
    {
        return $this->belongsTo(Structures::class, 'structure_id');
    }
}
