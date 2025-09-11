<?php

namespace App\Models\Creditos\Cuenta\Records;

use Illuminate\Database\Eloquent\Model;

class CuentaUsuarioRecord extends Model
{
    protected $connection = 'records';
    protected $table = 'cuenta_usuarios_records';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cuenta_id',
        'agencia_id',
        'monto_inicial',
        'monto_final',
        'fecha_inicio',
        'fecha_cierre',
        'cierre_id'

    ];
}
