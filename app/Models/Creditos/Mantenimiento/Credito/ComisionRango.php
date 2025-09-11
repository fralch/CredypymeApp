<?php

namespace App\Models\Creditos\Mantenimiento\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComisionRango extends Model
{
    use HasFactory;

    protected $table = 'credito_comisiones_rangos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'comision_id',
        'monto_desde',
        'monto_hasta',
        'monto_cobrar',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
