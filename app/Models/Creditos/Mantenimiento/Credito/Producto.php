<?php

namespace App\Models\Creditos\Mantenimiento\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'credito_productos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'producto',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
