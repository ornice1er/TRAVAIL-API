<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class AppelsOffre extends Model
{
     use HasFactory, Filterable;

    protected $fillable = [
        'label',
        'slug',
        'date_emission',
        'date_cloture',
        'representant_offre',
        'status',
        'media_id',
    ];

    protected static $whiteListFilter = ['*'];

    public static function getAllAppelsOffres()
    {
        return self::orderBy('id', 'DESC')->paginate(10);
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            // $model->id = Str::uuid(); // À décommenter si tu souhaites générer un UUID
        });
    }
}
