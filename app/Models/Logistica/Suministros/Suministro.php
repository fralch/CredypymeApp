<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suministro extends Model
{
    use SoftDeletes;
    protected $table = 'suministro_almacen';
    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_id',
        'codigo',
        'suministro',
        'marca',
        'detalle',
        'tipo_id',
        'proveedor_id',
        'condicion_id',
        'clasificacion',
        'cantidad',
        'medicion_id',
        'valor_unitario',
        'estado_id',
        'datos_creacion',
        'datos_actualizacion',

        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'valor_unitario' => 'float',
        'cantidad_asignar' => 'float',
        'cantidad_envio' => 'float',
        'cantidad_vender' => 'float',
        'valor_venta' => 'float',
    ];
}
