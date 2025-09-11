<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;
    protected $table = 'credito_notificaciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'credito_id',
        'tipo_id',
        'monto',
        'acumulado',
        'numero_cuota',
        'usuario_envio',
        'descripcion_envio',
        'estado',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
