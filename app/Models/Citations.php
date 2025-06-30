<?php

namespace App\Models;

use Auth;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Citations extends Model
{
    use HasFactory, Filterable, SoftDeletes;
    protected $fillable = ['title', 'resume', 'structure_id'];
}