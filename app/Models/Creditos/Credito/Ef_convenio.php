<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ef_convenio extends Model
{
    use HasFactory;
    protected $table = 'evaluacion_financiera_convenio';
    protected $primaryKey = 'id';

    protected $fillable = [
        'evaluacion_id',
        'sueldo_neto',
        'porcentaje_descuento',
        'otros_ingresos',
        'prestamos',

        'antecedentes_cliente',
        'referencias_laborales',
        'referencias_domicilio',
        'referencias_pariente_vecino',
        'referencias_aval',
        'destino_prestamo',
        'otros_comentarios',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
