<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Communique extends Model
{
    use HasFactory, Filterable;
    protected static $whiteListFilter = ['*'];

    protected $fillable = ['title', 'description', 'media_id', 'slug'];

    public function files()
    {
        return $this->hasMany(CommuniqueFile::class, 'communiques_id');
    }


     public function media()
    {
        return $this->belongsTo(Media::class,'media_id');
    }
}
