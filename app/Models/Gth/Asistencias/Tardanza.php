<?php

namespace App\Models\Gth\Asistencias;

use Illuminate\Database\Eloquent\Model;

class Tardanza extends Model
{
    protected $table = 'asistencia_tardanzas';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [

        'marcaje_id',
        'minutos',
        'justificado',




    ];
}
