<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Datos_aplicacion extends Model
{
    protected $table = 'datos_aplicacion';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
