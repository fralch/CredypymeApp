<?php

namespace App\Models\Creditos\Clientes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    protected $table = 'cliente_negocios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'cliente_id',
        'nombre',
        'actividad',
        'ciiu_id',
        'direccion',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'referencia_direccion',
        'telefonos',
        'vinculado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at',
    ];
    public $timestamps = true;
}
