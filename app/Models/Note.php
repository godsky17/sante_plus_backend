<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Note extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'notes';

    protected $fillable = [
        'id_medecin',           // ID du médecin auteur
        'contenu',
        'date',
        'id_dossier_medical',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'id_dossier_medical');
    }
}