<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class ActualitesFiles extends Model
{
        use HasFactory, Filterable;

    protected $fillable = ['type', 'nom', 'reference', 'filename', 'actualites_id'];

    public static function getAllActualitesFiles()
    {
        return self::orderBy('id', 'DESC')->paginate(10);
    }

    public function actualite()
    {
        return $this->belongsTo(Actualite::class, 'actualites_id');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            // $model->id = Str::uuid(); // Active si tu utilises des UUID
        });
    }

}
