<?php

namespace App\Models\Creditos\Mantenimiento\Album;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $table = 'cliente_album_categorias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'categoria',
        'descripcion',
        'nuevo',

        'datos_creacion',
        'datos_actualizacion',

        'created_at',
        'updated_at'
    ];

    public $timestamps = true;
}
