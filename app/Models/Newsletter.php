<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Newsletter extends Model
{
    use HasFactory, Filterable, SoftDeletes, HasUuids;
    protected $fillable = [
        'titre',
        'ajouté_par',
        'status',
        'structure_id',
    ];

    /**
     * Vérifie si un email est déjà inscrit.
     *
     * @param string $email
     * @return bool
     */
    public static function isSubscribed($email)
    {
        $check = self::whereTitre($email)->first();
        return $check ? true : false;
    }

    /**
     * Relation vers l'utilisateur ayant ajouté la newsletter.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'ajouté_par');
    }

    /**
     * Relation vers la structure.
     */
    public function structure()
    {
        return $this->belongsTo(Structures::class, 'structure_id');
    }
}
