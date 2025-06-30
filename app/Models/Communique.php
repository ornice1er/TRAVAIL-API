<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Communiques extends Model
{
    use HasFactory, Filterable, SoftDeletes, HasUuids;

    protected $fillable = ['title', 'description', 'media_id', 'slug'];

    public function files()
    {
        return $this->hasMany(CommuniquesFiles::class, 'communiques_id');
    }
}
