<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Structure extends Model
{
    use Filterable, HasFactory, HasUuids;
    protected $fillable = [
        'name',
        'acronym',
        'slug',
        'name_responsable',
        'photo_responsable',
        'biographie_responsable',
        'photo',
        'phone',
        'email',
        'vision',
        'type_structure_id',
        'fonction',
        'historique',
        'responsable_text'
    ];

    // Relation récursive : une structure peut avoir plusieurs sous-structures
    public function structure_info()
    {
        return $this->hasMany(Structures::class, 'structure_id', 'id');
    }
    
    // Récupérer une structure avec ses sous-structures
    public static function getAllStructure($id)
    {
        return self::with('structure_info')->find($id);
    }

    public function mots()
    {
        return $this->hasMany(Mot::class, 'structure_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'structure_id');
    }

    public function teams1()
    {
        return $this->hasMany(Team::class, 'structure_id')->where('type', '!=', 'autre');
    }

    public function teams2()
    {
        return $this->hasMany(Team::class, 'structure_id')->where('type', 'autre');
    }

    public function citations()
    {
        return $this->hasMany(Citations::class, 'structure_id');
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'structure_id')->where('type', 'doc');
    }

    public function myAof()
    {
        return $this->hasOne(Media::class, 'structure_id')
            ->where('type', 'aof')
            ->where('is_published', true)
            ->orderBy('id', 'desc');
    }

    public function mediaPrestations()
    {
        return $this->hasMany(Media::class, 'structure_id')->where('type', 'prestation');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'structure_id');
    }
}
