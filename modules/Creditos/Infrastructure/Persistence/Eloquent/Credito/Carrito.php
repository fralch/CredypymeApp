<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;
    protected $table = 'credito_carritos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'fecha_apertura',
        'fecha_cierre',
        'pagado',
        'anulado',
        'comentario_anulado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
