<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'credito_estados';
    protected $primaryKey = 'id';
    protected $fillable = [
        'estado',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
