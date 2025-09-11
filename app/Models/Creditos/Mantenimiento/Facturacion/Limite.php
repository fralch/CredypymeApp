<?php

namespace App\Models\Creditos\Mantenimiento\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Limite extends Model
{
    use HasFactory;
    protected $table = 'facturacion_limites';
    protected $primaryKey = 'id';
    protected $fillable = [
        'año',
        'mes',
        'limite_ideal',
        'total_emitido',
        'restante',
        'limite_real',
        'comprobantes_emitidos',
        'comprobantes_mes_anterior',
        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
