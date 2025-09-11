<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ef_activo_corriente extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_financiera_activo_corriente';
    protected $primaryKey = 'id';

    protected $fillable = [
        'evaluacion_id',
        'caja',
        'adelantos',
        'bancos',
        'varios',
        'cuentas_cobrar',
        'inventario_mercaderia',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
