<?php

namespace App\Models;

use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\Model;

class RendezVous extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'rendez_vous';

    protected $fillable = [
        'id_patient',
        'id_medecin',
        'date_heure',
        'type_consultation',
        'statut',
        'est_payé',
        'motif',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
        'est_payé' => 'boolean',
    ];

}
