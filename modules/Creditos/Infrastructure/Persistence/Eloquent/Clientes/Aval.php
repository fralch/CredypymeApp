<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Aval extends Model
{
    use HasFactory;
    protected $table = 'cliente_avales';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente_id',
        'agencia_aval',
        'aval_id',
        'vinculado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;
}
