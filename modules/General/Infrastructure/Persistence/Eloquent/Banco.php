<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $table = 'bancos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'agencia_id',
        'banco',
        'titular',
        'numero',
        'cci',
        'acumulado',
        'detalle',
        'habilitado',

        'datos_creacion',
        'datos_actualizacion',

        'created_at',
        'updated_at'
    ];

    protected $casts = ['habilitado' => 'boolean'];
}
