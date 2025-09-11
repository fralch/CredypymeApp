<?php

namespace App\Models\Logistica\Activos;

use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    protected $table = 'activo_inventario';
    protected $primaryKey = 'id';
    protected $fillable = [
        'agencia_id',
        'nombre_id',
        'codigo',
        'tipo_id',
        'responsable_id',
        'ubicacion_id',
        'descripcion',
        'cantidad',
        'marca',
        'modelo',
        'placa',
        'caracteristicas',
        'color',
        'condicion_id',
        'estado_id',
        'fecha_compra',
        'valor_compra',
        'vida_util',
        'porcentaje_depreciacion',
        'valor_actual',
        'fecha_ultimo_inventario',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
