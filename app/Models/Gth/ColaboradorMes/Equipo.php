<?php

namespace App\Models\Gth\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    //
    // public $timestamps = false;
    protected $table = 'colaboradormes_equipos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'equipo',
        'responsable_id',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
