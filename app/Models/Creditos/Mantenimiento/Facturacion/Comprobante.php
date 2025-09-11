<?php

namespace App\Models\Creditos\Mantenimiento\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;
    protected $table = 'facturacion_comprobantes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'comprobante',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
