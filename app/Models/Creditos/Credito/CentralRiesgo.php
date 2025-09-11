<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralRiesgo extends Model
{
    use HasFactory;
    protected $table = 'credito_central_riesgo';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo_riesgo',
        'nombre_breve',
        'dias_desde',
        'dias_hasta',
        'por_defecto',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
