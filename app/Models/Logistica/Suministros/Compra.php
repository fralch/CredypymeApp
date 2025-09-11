<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'suministro_compras';

    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_id',
        'usuario_compra',
        'documento',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
