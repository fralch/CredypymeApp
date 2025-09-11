<?php

namespace App\Models\Gth\Asistencias;

use Illuminate\Database\Eloquent\Model;

class Falta extends Model
{
    protected $table = 'asistencia_faltas';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [

        'usuario_id',
        'fecha',
        'turno',
        'justificado',




    ];
}
