<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Corps extends Model
{
    use Filterable,HasFactory,SoftDeletes;

    private static $whiteListFilter = ['*'];

    protected $fillable = ['code', 'libelle'];

    protected $dates = ['deleted_at'];

    protected $casts = ['data' => 'array'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            // $model->id = Str::uuid();
        });
    }
}
