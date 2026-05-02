<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros\Records;

use Illuminate\Database\Eloquent\Model;

class SuministroRecord extends Model
{
    protected $connection = 'records';
    protected $table = 'suministro_almacen_records';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'agencia_id',
        'suministro_id',
        // 'codigo',
        // 'suministro',
        // 'tipo_id',
        // 'proveedor_id',
        // 'condicion_id',
        'cantidad',
        'valor_unitario',
        'estado_id',
        // 'datos_actualizacion_origen',
        // 'updated_at_origen',
        'cierre_id',
        'created_at'

    ];
}
