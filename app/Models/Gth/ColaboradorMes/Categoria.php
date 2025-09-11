<?php

namespace App\Models\Gth\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    protected $table = 'colaboradormes_categorias';
    protected $primaryKey = 'id';
    protected $fillable = [
        'categoria',
        'descripcion',
        'peso',
        'nivel_id',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
