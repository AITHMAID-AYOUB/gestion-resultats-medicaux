<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrateur extends Model
{
    protected $table = 'administrateurs';

    protected $fillable = [
        'email',
        'password',
    ];

    public $timestamps = false;
}