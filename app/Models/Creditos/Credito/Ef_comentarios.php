<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ef_comentarios extends Model
{
    use HasFactory;
    protected $table = 'evaluacion_financiera_comentarios';
    protected $primaryKey = 'id';

    protected $fillable = [
        'evaluacion_id',
        'antecedentes_cliente',
        'referencias_negocio',
        'referencias_domicilio',
        'referencias_familiar_vecino',
        'referencias_pariente',
        'referencias_aval',
        'destino_prestamo',
        'otros_comentarios',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
