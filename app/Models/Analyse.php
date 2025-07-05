<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Analyse extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'analyses';

    protected $fillable = [
        'id_patient',
        'id_medecin',
        'date',
        'fichier_resultat',
        'avis_medecin',
        'est_consultée_par_medecin',
    ];

    protected $casts = [
        'date' => 'datetime',
        'est_consultée_par_medecin' => 'boolean',
    ];

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'id_dossier_medical');
    }
}
