<?php

namespace App\Models\Creditos\Mantenimiento\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subproducto extends Model
{
    use HasFactory;

    protected $table = 'credito_subproductos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'subproducto',
        'producto_id',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
