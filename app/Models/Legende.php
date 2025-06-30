<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Legende extends Model
{
    use HasFactory, Filterable, SoftDeletes, HasUuids;
    protected $fillable = [
        'title',
        'organigramme_id'
    ];
}

