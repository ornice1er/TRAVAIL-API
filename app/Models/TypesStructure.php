<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TypesStructure extends Model
{
    use Filterable, HasFactory, HasUuids;
    protected $fillable = [
        'title', 
        'is_parent'];

    public function structures()
    {
        return $this->hasMany(Structures::class, 'type_structure_id');
    }
}
