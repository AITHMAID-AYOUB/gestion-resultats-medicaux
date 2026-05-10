<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';

    protected $fillable = [
        'patient_id',
        'biologiste_id',
        'date_rdv',
        'heure_rdv',
        'motif',
        'statut',
    ];
}