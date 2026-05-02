<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;

    protected $table = 'cliente_transferencias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dni',
        'cliente',
        'agencia_origen',
        'asesor_origen',
        'agencia_destino',
        'asesor_destino',
        'evaluacion_financiera',
        'album_fotos',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
