<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DossierMedical extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'dossiers_medicals';

    protected $fillable = [
        'id_patient',
        'consultable_par_hopital'
    ];

    protected $casts = [
        'consultable_par_hopital' => 'boolean',
    ];

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class, 'id_dossier_medical');
    }

    // Relation: Un dossier médical a plusieurs analyses
    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'id_dossier_medical');
    }

    // Relation: Un dossier médical a plusieurs notes
    public function notes()
    {
        return $this->hasMany(Note::class, 'id_dossier_medical');
    }
}
