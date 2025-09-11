<?php

namespace App\Models\Creditos\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'cliente_movimientos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dni',
        'cliente',
        'asesor_origen',
        'asesor_destino',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
