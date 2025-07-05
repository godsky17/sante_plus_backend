<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use MongoDB\Laravel\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Authenticatable, HasApiTokens;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    // Champs assignables en masse
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'contact',
        'adresse',
        'genre',
        'photo_profil',
        'date_naissance',
        'type_utilisateur',
        'statut',
        'date_inscription',
        'specialites',
        'id_hopital',
        'est_independant',
        'documents_justificatifs',
        'planning_id',
        'services',
        'medecins_affilies',
        'coordonnees',
        'invitation_validee',
        'niveau_acces',
        'preferences_notification',
        'api_token'
    ];

    // Champs à cacher dans la sérialisation (ex: password)
    protected $hidden = [
        'mot_de_passe',
        'api_token'
    ];

    // Type des attributs (casting)
    protected $casts = [
        'date_naissance' => 'date',
        'date_inscription' => 'datetime',
        'est_independant' => 'boolean',
        'invitation_validee' => 'boolean',
        'preferences_notification' => 'array',
        'specialites' => 'array',
        'services' => 'array',
        'medecins_affilies' => 'array',
        'documents_justificatifs' => 'array',
        'coordonnees' => 'array',
        'niveau_acces' => 'int',
        'id_hopital' => 'string',
        'planning_id' => 'string',
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    public function hopital()
    {
        return $this->belongsTo(Hopital::class, 'id_hopital', '_id');
    }

    public function planning()
    {
        return $this->belongsTo(Planning::class, 'planning_id', '_id');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }
}
