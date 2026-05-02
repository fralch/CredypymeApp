<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    use HasFactory;
    protected $table = 'sesiones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'conectado',
        'get_id', 
        'datos_sesion',
        'ultima_accion',
        'created_at',
        'updated_at',

    ];

    public $timestamps = true;
}
