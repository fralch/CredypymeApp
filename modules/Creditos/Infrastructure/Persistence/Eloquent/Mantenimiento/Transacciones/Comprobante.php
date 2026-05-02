<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Transacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;
    protected $table = 'transaccion_comprobantes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'comprobante',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion'
    ];
}
