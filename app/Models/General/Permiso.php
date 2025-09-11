<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = [
        'modulo',
        'area',
        'datos_creacion',
        'datos_actualizacion',
    ];
}
