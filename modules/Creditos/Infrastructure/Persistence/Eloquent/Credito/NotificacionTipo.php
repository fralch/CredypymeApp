<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionTipo extends Model
{
    use HasFactory;
    protected $table = 'credito_notificaciones_tipos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'monto',
        'descripcion',
        'archivo',
        'orden',
        'habilitado',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
