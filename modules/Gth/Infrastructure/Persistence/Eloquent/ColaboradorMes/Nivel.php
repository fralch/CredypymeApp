<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    //
    protected $table = 'colaboradormes_niveles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nivel',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
