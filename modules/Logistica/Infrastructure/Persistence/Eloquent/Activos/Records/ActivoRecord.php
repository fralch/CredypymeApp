<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos\Records;

use Illuminate\Database\Eloquent\Model;

class ActivoRecord extends Model
{
    protected $connection = 'records';
    protected $table = 'activo_inventario_records';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'activo_id',
        'agencia_id',
        // 'nombre_id',
        // 'codigo',
        // 'tipo_id',
        // 'responsable_id',
        // 'ubicacion_id',
        // 'descripcion',
        // 'cantidad',
        // 'marca',
        // 'modelo',
        // 'placa',
        // 'caracteristicas',
        // 'color',
        // 'condicion_id',
        // 'estado_id',
        // 'fecha_compra',
        // 'valor_compra',
        // 'vida_util',
        // 'porcentaje_depreciacion',
        'valor_actual',
        // 'fecha_ultimo_inventario',
        // 'datos_actualizacion_origen',
        // 'updated_at_origen',
        'cierre_id',
        'created_at'

    ];
}
