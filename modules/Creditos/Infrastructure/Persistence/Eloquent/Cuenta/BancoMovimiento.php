<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta;

use Illuminate\Database\Eloquent\Model;

class BancoMovimiento extends Model
{
    protected $table = 'banco_movimientos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'banco_id',
        'tipo',
        'agencia_credito',
        'credito_id',
        'agencia_inversion',
        'inversion_id',
        'operacion',
        'modo',
        'monto',
        'fecha_movimiento',
        'agencia_operacion',
        'caja_operacion',
        'cuenta_operacion',
        'banco_operacion',
        'descripcion',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;
}
