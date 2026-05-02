<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compromiso extends Model
{
    use HasFactory;
    protected $table = 'credito_compromisos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'credito_id',
        'compromiso',
        'fecha_hora_visita',
        'fecha_vencimiento',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
