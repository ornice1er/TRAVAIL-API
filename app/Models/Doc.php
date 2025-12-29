<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Doc extends Model
{
    use Filterable, HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'status',
        'media_id',
        'filename',
    ];

      public static function boot()
    {
        parent::boot();

        // Avant création, génération code unique et nom complet
        self::creating(function ($model) {
            $model->slug = Str::slug($model->name).' '.uniqId();
        });
    }

         public function media()
    {
        return $this->belongsTo(Media::class,'media_id');
    }
    
}