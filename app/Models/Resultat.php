<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultat extends Model
{
    protected $table = 'resultats';

    protected $fillable = [
        'patient_id',
        'biologiste_id',
        'fichier_pdf',
        'commentaire',
        'date_resultat',
        
    ];

    public $timestamps = false;
}
