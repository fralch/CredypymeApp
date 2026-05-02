<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaTrabajo extends Model
{
    use HasFactory;
    protected $table = 'areas_trabajo';
    protected $primaryKey = 'id';

    protected $fillable = [
        'area',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
    ];

    public $timestamps = true;
}
