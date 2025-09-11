<?php

namespace App\Models\Creditos\Inversion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InversionMeta extends Model
{
    use HasFactory;
    protected $table = 'inversion_meta_registros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'producto_meta_id',
        'cliente_id',
        'comentario',
        'valor_meta',
        'acumulado',
        'fecha_movimiento',
        'fecha_apertura',
        'fecha_cierre',
        'agencia_caja_apertura',
        'caja_apertura',
        'agencia_caja_cierre',
        'caja_cierre',
        'comentario_cierre',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at',
    ];
}
