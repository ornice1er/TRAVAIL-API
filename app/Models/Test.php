<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Test extends Model
{
    use HasFactory, Filterable;
    protected static $whiteListFilter = ['*'];

    protected $fillable = ['title', 'description', 'media_id', 'communiques','has_principal_access'];

    protected $appends = ['lists'];


      protected $casts=[
        'communiques'=>'array'
      ];

    public function files()
    {
        return $this->hasMany(TestFile::class, 'test_id');
    }

        public function getListsAttribute()
        {
            if (empty($this->communiques)) {
                return collect();
            }

            return Communique::with('media')
                ->whereIn('id', $this->communiques)
                ->get();
        }


     public function media()
    {
        return $this->belongsTo(Media::class,'media_id');
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
