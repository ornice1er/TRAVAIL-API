<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Doc extends Model
{
    use Filterable, HasFactory, HasUuids;
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'status',
        'media_id',
        'filename',
    ];
}