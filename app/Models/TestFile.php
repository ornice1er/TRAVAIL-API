<?php

namespace App\Models;


use Str;
use App\Models\Tests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;

class TestFile extends Model
{
    use HasFactory, Filterable;
    protected $fillable = ['type', 'title', 'filename', 'test_id'];

    // Normalement, un fichier lié à un communiqué est une relation "belongsTo" vers Tests,
    // pas "hasMany" vers lui-même. Je corrige donc en relation correcte :

    public function test()
    {
        return $this->belongsTo(Test::class, 'tests_id');
    }
}
