<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Mantenimiento\Solicitudes;

use Illuminate\Database\Eloquent\Model;

class LicenciaCategoria extends Model
{
    //
    protected $table = 'licencia_categorias';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'abreviacion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
