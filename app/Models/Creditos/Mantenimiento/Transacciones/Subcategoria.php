<?php

namespace App\Models\Creditos\Mantenimiento\Transacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;
    protected $table = 'transaccion_subcategorias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'subcategoria',
        'categoria_id',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion'
    ];
}
