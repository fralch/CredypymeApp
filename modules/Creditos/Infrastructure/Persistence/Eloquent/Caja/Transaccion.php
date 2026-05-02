<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;
    protected $table = 'transaccion_registros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'categoria_id',
        'subcategoria_id',
        'agencia_id',
        'usuario_id',
        'area_trabajo_id',
        'comprobante_id',
        'caja_id',
        'monto',
        'concepto',
        'documento',

        'fecha_transaccion',
        'regularizado',
        'es_real',

        'datos_creacion',
        'datos_actualizacion',
    ];

    public $timestamps = true;
}
