<?php

namespace App\Models\Creditos\Mantenimiento\Inversion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosMeta extends Model
{
    use HasFactory;

    protected $table = 'inversion_productos_meta';
    protected $primaryKey = 'id';
    protected $fillable = [
        'producto',
        'valor_meta', 
        'descripcion',
        'orientacion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
