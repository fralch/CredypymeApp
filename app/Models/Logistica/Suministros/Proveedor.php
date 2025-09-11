<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'suministro_proveedores';

    protected $primaryKey = 'id';

    protected $fillable = [
        'proveedor',
        'direccion',
        'representante',
        'ruc',
        'dni',
        'telefono',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
    //
}
