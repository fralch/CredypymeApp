<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja;

use Illuminate\Database\Eloquent\Model;

class AdelantoHaber extends Model
{
    protected $table = 'transaccion_adelanto_haberes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'descripcion',
        'monto',
        'agencia_caja',
        'caja_id',
        'fecha_adelanto',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
