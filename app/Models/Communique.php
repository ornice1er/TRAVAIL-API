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


   public function concours()
    {
           return Test::with('files')->where('communiques', 'LIKE', '%'.$this->id.'%')->first();

    }


    

        /**
     * Fonction boot pour générer un code unique avant la création
     */
    public static function boot()
    {
        parent::boot();

        // Avant création, génération code unique et nom complet
        self::creating(function ($model) {
            $model->slug = Str::slug($model->title).' '.uniqId();
        });
    }

}
