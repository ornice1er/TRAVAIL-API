<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\QueryBuilder\QueryBuilder;

class Prime extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table associée au modèle.
     */
    protected $table = 'primes';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'nom',
        'description',
        'montant',
        'status',
        'file'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => 'integer',
        'montant' => 'decimal:2'
    ];

    /**
     * Relation avec les primestatuts.
     */
    public function primestatuts()
    {
        return $this->hasMany(Primestatut::class);
    }

    /**
     * Scope pour filtrer par statut actif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope pour filtrer par statut inactif.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Query Builder avec filtres.
     */
    public static function ignoreRequest(array $ignored = [])
    {
        return QueryBuilder::for(static::class)
            ->allowedFilters([
                'nom',
                'description',
                'status',
                'montant'
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
                'nom',
                'montant',
                'status'
            ]);
    }
}