<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class CommuniqueFile extends Model
{
    use HasFactory, Filterable, HasUuids;
    protected $fillable = ['type', 'nom', 'reference', 'filename', 'communiques_id'];

    // Normalement, un fichier lié à un communiqué est une relation "belongsTo" vers Communiques,
    // pas "hasMany" vers lui-même. Je corrige donc en relation correcte :

    public function communique()
    {
        return $this->belongsTo(Communique::class, 'communiques_id');
    }
}
