<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class RecrutementFile extends Model
{
    use Filterable, HasFactory, SoftDeletes, HasUuids;
    protected $fillable = [
        'is_result_file',
        'type',
        'nom',
        'reference',
        'filename',
        'recrutement_id',
    ];

    public function recrutement()
    {
        return $this->belongsTo(Recrutement::class, 'recrutement_id');
    }
}
