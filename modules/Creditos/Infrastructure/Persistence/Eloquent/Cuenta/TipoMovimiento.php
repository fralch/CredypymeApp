<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    use HasFactory;
    protected $table = 'cuenta_tipo_movimientos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
