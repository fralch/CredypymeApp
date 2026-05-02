<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{
    protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = [
        'fecha',
        'motivo',
        'agencias',
        'datos_creacion',
        'datos_actualizacion',
    ];
}
