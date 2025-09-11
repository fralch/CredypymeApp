<?php

namespace App\Models\Creditos\Cuenta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaUsuario extends Model
{
    use HasFactory;
    protected $table = 'cuenta_usuarios';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dni',
        'con_cuenta',
        'monto',
        'datos_creacion',
        'datos_actualizacion'
    ];

    public $timestamps = true;
}
