<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    protected $primaryKey = 'id_agencia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'distrito_id',
        'provincia_id',
        'departamento_id',
        'celular',
        'telefono',
        'cuentas',
        'nueva_empresa'
    ];

    protected $cast = ['nueva_empresa' => 'boolean'];
}
