<?php

namespace App\Models\General\Credicheck;

use Illuminate\Database\Eloquent\Model;

class Promociones extends Model
{
    protected $table = 'credicheck_promociones';
    protected $primaryKey = 'id';
    protected $fillable = [
        'titulo_vista',
        'titulo_detalle',
        'subtitulo_vista',
        'subtitulo_detalle',
        'oferta_vista',
        'oferta_detalle',
        'descripcion_vista',
        'descripcion_detalle',
        'empresa',
        'lugar',
        'stock',
        'vigencia',
        'habilitado',
        'imagen_vista',
        'imagen_detalle',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at',
    ];
}
