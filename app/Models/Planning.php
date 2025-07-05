<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Planning extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'plannings';

    protected $fillable = [
        'medecin_id',
        'creneaux_disponibles', // stocké en JSON
    ];

    protected $casts = [
        'creneaux_disponibles' => 'array',
    ];
}
