<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Hopital extends Model
{
    protected $collection = 'hopitals';

    protected $fillable = [
        'nom',
        'services',
        'localisation',
        'medecins_affilies_ids',
        'statistiques',
    ];

    protected $casts = [
        'services' => 'array',
        'localisation' => 'array',
        'medecins_affilies_ids' => 'array',
        'statistiques' => 'array',
    ];

    public function medecinsAffilies()
    {
        return $this->hasMany(User::class, '_id', 'medecins_affilies_ids');
    }
}
