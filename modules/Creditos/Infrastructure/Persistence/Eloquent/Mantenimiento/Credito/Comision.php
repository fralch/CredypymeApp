<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{
    use HasFactory;

    protected $table = 'credito_comisiones';
    protected $primaryKey = 'id';
    protected $fillable = [
        'comision',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
