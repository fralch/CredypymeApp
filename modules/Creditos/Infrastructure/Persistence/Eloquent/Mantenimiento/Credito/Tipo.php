<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    use HasFactory;

    protected $table = 'credito_tipos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tipo',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
