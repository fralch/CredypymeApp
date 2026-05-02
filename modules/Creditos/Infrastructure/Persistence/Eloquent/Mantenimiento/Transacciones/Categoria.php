<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'transaccion_categorias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'categoria',
        'descripcion',
        'tipo',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion'
    ];

    protected $casts = [
        'habilitado' => 'boolean'
    ];
}
