<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcours extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table associée au modèle.
     */
    protected $table = 'parcours';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'libelle',
        'media_id'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'media_id' => 'integer'
    ];

    /**
     * Relation avec le modèle Media.
     */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}