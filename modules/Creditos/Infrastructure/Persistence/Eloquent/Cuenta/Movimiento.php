<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;
    protected $table = 'cuenta_movimientos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cuenta_id',
        'tipo',
        'monto',
        'descripcion',
        'tipo_movimiento_id',
        'fecha_movimiento',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
