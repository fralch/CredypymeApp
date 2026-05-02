<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ef_flujo_caja extends Model
{
    use HasFactory;
    protected $table = 'evaluacion_financiera_flujo_caja';
    protected $primaryKey = 'id';

    protected $fillable = [
        'evaluacion_id',
        'ventas_detallado',
        'ventas_monto',
        'costo_ventas',
        'costos_operativos',
        'otros_ingresos',
        'gastos_familiares',
        'prestamos',
        'vehiculos',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
