<?php

namespace App\Models;

use App\Services\StatutAgentService;
use App\Utilities\Core;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable; // this sould be imported
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements JWTSubject
{
    use Filterable, HasApiTokens, HasFactory, Notifiable, HasRoles;

    // Liste blanche des attributs pouvant être filtrés
    private static $whiteListFilter = ['*'];

    // Les attributs qui sont mass-assignable
    protected $fillable = [
        'code',
        'name',
        'lastname',
        'firstname',
        'birthdate',
        'birthplace',
        'address',
        'phone',
        'photo',
        'is_active',
        'is_first_connexion',
        'email',
        'password',
        'token',
        'email_verified_at',
        'code_otp',
        'project_id',
        'spoken_languages',
        'understood_languages',
        'municipality_id',
        'statut_agent_id',
        'residence_place',
        'education_level',
        'nb_children',
        'computer_skills',
        'reference_person',
        'comment',
        'cv',
        'push_token',
        'structure_id',        // ajouté ici
    ];

    // Champs à cacher lors de la sérialisation
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Type de cast des attributs
    protected $casts = [
        'email_verified_at' => 'datetime',
        'spoken_languages' => 'array',
        'understood_languages' => 'array',
    ];

    protected $appends = [];

    protected $dates = ['deleted_at'];

    /**
     * Fonction boot pour générer un code unique avant la création
     */
    public static function boot()
    {
        parent::boot();

        // Avant création, génération code unique et nom complet
        self::creating(function ($model) {
            $model->code = (string) Core::generateIncrementUniqueCode('users', 3, 'code', null);
            $model->name = trim($model->lastname . ' ' . $model->firstname);
        });
    }

    // Mutators pour mettre à jour le nom complet automatiquement
    public function setFirstnameAttribute($value)
    {
        $this->attributes['firstname'] = $value;
        $this->updateFullName();
    }

    public function setLastnameAttribute($value)
    {
        $this->attributes['lastname'] = $value;
        $this->updateFullName();
    }

    private function updateFullName()
    {
        $firstname = $this->attributes['firstname'] ?? '';
        $lastname = $this->attributes['lastname'] ?? '';
        $this->attributes['name'] = trim("$lastname $firstname");
    }

    // Relation avec la structure
    public function structure()
    {
        return $this->belongsTo(Structures::class, 'structure_id');
    }

    // Relation avec media (ajouté par l'utilisateur)
    public function media()
    {
        return $this->hasMany(Media::class, 'adding_by');
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class, 'user_id');
    }

    // Méthodes nécessaires pour JWTAuth
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }

    /**
     * Spécifie le token FCM de l'utilisateur pour les notifications
     *
     * @return string|array|null
     */
    public function routeNotificationForFcm()
    {
        info($this->push_token);
        return $this->push_token;
    }
}