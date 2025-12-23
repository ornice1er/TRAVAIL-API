<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Team extends Model
{
    use Filterable, HasFactory;

    protected $guarded = [];

    public function structure()
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }
}
