<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TypeStructure extends Model
{
    use Filterable, HasFactory, HasUuids;

        protected $table = 'types_structures';


    protected $fillable = [
        'title', 
        'is_parent'];

    public function structures()
    {
        return $this->hasMany(Structure::class, 'type_structure_id');
    }
}
