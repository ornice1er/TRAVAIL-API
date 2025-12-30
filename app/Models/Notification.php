<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Notification extends Model
{
use HasFactory, Filterable;
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'description',
        'lu_à',
        'sent_to',
    ];

    /**
     * Polymorphic relation vers le modèle notifiable.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Relation vers l'utilisateur destinataire.
     */
    public function destinataire()
    {
        return $this->belongsTo(User::class, 'sent_to');
    }

    /**
     * Scope pour les notifications non lues.
     */
    public function scopeNonLues($query)
    {
        return $query->whereNull('lu_à');
    }
}