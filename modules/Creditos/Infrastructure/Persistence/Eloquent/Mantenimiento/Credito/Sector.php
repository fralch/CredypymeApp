<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'credito_sectores';
    protected $primaryKey = 'id';
    protected $fillable = [
        'sector',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
