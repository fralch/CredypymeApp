<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Cierre extends Model
{
    protected $table = 'aplicacion_cierres';
    protected $primaryKey = 'id';
    protected $fillable = [
        'modulo_id',
        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
