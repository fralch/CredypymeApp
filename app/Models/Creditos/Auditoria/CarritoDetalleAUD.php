<?php

namespace App\Models\Creditos\Auditoria;

use Illuminate\Database\Eloquent\Model;

class CarritoDetalleAUD extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'credito_carrito_detalles_aud';

    protected $fillable = [
        'registro_id',
        'historial',
        'fecha_registro',
        'datos_creacion',
        'created_at',
        'updated_at'

    ];
}
