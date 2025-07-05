<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hopital;

class HopitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hopital1 = new Hopital();
        $hopital1->nom = "Hôpital Saint-Pierre";
        $hopital1->services = ['Cardiologie', 'Pédiatrie', 'Radiologie'];
        $hopital1->localisation = ['lat' => 48.8566, 'lng' => 2.3522];
        $hopital1->medecins_affilies_ids = [];
        $hopital1->statistiques = [
            'nombrePatients' => 1200,
            'tauxOccupation' => 85,
            'consultationsMensuelles' => 300,
        ];
        $hopital1->save();

        $hopital2 = new Hopital();
        $hopital2->nom = "Clinique du Parc";
        $hopital2->services = ['Orthopédie', 'Neurologie'];
        $hopital2->localisation = ['lat' => 45.7640, 'lng' => 4.8357];
        $hopital2->medecins_affilies_ids = [];
        $hopital2->statistiques = [
            'nombrePatients' => 800,
            'tauxOccupation' => 75,
            'consultationsMensuelles' => 150,
        ];
        $hopital2->save();
    }
}
