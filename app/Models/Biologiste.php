<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biologiste extends Model
{
    protected $table = 'biologistes';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
    ];
}