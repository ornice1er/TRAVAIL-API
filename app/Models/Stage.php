<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Stage extends Model
{
    use Filterable, HasFactory, SoftDeletes, HasUuids;
    protected $fillable = [
        'status',
        'media_id',
        'year',
        'country',
        'period_start',
        'period_end',
        'city',
        'closing_date',
        'name',
        'delay',
        'slug',
        'structure',
        'resume'
    ];

    /**
     * Relation vers le média associé.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
