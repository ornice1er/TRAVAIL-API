<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\QueryBuilder\Filters\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class Primestatut extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table associée au modèle.
     */
    protected $table = 'primestatuts';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'statut_id',
        'prime_id',
        'nom',
        'lieu',
        'type_Primestatut',
        'link_token',
        'code',
        'lien_unique',
        'qr_code',
        'media_token',
        'lien_unique_media',
        'qr_code_media',
        'status',
        'description'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => 'integer',
        'statut_id' => 'integer',
        'prime_id' => 'integer'
    ];

    /**
     * Relation avec le modèle Statut.
     */
    public function statut()
    {
        return $this->belongsTo(Statut::class);
    }

    /**
     * Relation avec le modèle Prime.
     */
    public function prime()
    {
        return $this->belongsTo(Prime::class);
    }

    /**
     * Relation avec les invitations.
     */
    public function invites()
    {
        return $this->hasMany(Invite::class, 'Primestatut_id');
    }

    /**
     * Scope pour filtrer par statut.
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
                'lieu',
                'type_Primestatut',
                'status',
                'statut_id',
                'prime_id'
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
                'nom',
                'lieu',
                'status'
            ]);
    }
}