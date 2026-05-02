<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pariente extends Model
{
    use HasFactory;
    protected $table = 'cliente_parientes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente_id',
        'agencia_pariente',
        'pariente_id',
        'vinculado',
        'parentesco',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;
}
