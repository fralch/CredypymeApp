<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias;

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
