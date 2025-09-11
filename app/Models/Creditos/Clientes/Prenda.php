<?php

namespace App\Models\Creditos\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prenda extends Model
{
    use HasFactory;

    protected $table = 'cliente_prendas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente_id',
        'cantidad',
        'descripcion',
        'marca',
        'modelo',
        'serie',
        'color',
        'estado',
        'fecha_compra',
        'numero_comprobante',
        'precio_compra',
        'precio_actual',
        'disponible',
        'entregado',
        'acta_entrega',
        'comentario',
        'fotos',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
