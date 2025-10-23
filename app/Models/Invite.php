<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invite extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table associée au modèle.
     */
    protected $table = 'invites';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'Primestatut_id',
        'nom',
        'prenom',
        'phone',
        'email',
        'message',
        'status'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => 'integer',
        'Primestatut_id' => 'integer'
    ];

    /**
     * Relation avec le modèle Primestatut.
     */
    public function primestatut()
    {
        return $this->belongsTo(Primestatut::class, 'Primestatut_id');
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
}