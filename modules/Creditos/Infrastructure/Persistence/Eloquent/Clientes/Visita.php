<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;
    protected $table = 'cliente_visitas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'cliente_id',
        'motivo',
        'comentario',
        'fecha_hora_visita',
        'datos_creacion',

        'created_at',
        'updated_at'
    ];
}
