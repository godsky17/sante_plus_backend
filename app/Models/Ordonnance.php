<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Ordonnance extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ordonnances';

    protected $fillable = [
        'id_medecin',
        'id_patient',
        'date',
        'contenu',
        'fichier_pdf',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'id_dossier_medical');
    }
}
