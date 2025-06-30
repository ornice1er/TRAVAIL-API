<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Recrutement extends Model
{
    use Filterable, HasFactory, SoftDeletes, HasUuids;
    protected $fillable = [
        'title',
        'announcement_date',
        'test_date',
        'exam_place',
        'slug',
        'resume',
        'has_result',
        'status',
        'media_id',
    ];

    /**
     * Relation avec les fichiers du recrutement.
     */
    public function files()
    {
        return $this->hasMany(RecrutementFile::class, 'recrutement_id');
    }

    /**
     * Relation avec le média associé.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}