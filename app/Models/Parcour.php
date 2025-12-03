<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Parcour extends Model
{
    use HasFactory, Filterable, SoftDeletes;
    protected $fillable = [
        'libelle',
        'media_id',
    ];

    /**
     * Relation avec le média associé.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
