<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'action_name',
        'description',
        'done_by',
        'origin'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les valeurs possibles pour le champ origin
     */
    const ORIGIN_AUTH = 'AUTH';
    const ORIGIN_EDITION = 'EDITION';
    const ORIGIN_ACTIVITE = 'ACTIVITE';
    const ORIGIN_RAPPORT = 'RAPPORT';

    /**
     * Obtenir toutes les origines possibles
     */
    public static function getOrigins(): array
    {
        return [
            self::ORIGIN_AUTH,
            self::ORIGIN_EDITION,
            self::ORIGIN_ACTIVITE,
            self::ORIGIN_RAPPORT,
        ];
    }

    /**
     * Relation avec l'utilisateur qui a effectué l'action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    /**
     * Scope pour filtrer par origine
     */
    public function scopeByOrigin($query, $origin)
    {
        return $query->where('origin', $origin);
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('done_by', $userId);
    }

    /**
     * Scope pour les logs récents
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
