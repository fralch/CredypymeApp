<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Cargos_permiso extends Model
{
    protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = [
        'cargo_id',
        'permiso_id',
        'acceso_agencias',
        'datos_creacion',
        'datos_actualizacion',
    ];
}