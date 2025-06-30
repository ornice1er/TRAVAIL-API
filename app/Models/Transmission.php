<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Transmission extends Model
{
    use Filterable, HasFactory, SoftDeletes, HasUuids;
    protected $fillable = [
        'libelle', 
        'is_last', 
        'media_id', 
        'from', 
        'to'];
}
