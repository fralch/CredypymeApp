<?php

namespace App\Models\Gth\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    //
    //public $timestamps = false;
    protected $table = 'colaboradormes_examenes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'examen',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
