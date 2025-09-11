<?php

namespace App\Models\Creditos\Clientes;

use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    protected $table = 'cliente_registros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dni',
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'fecha_nacimiento',
        'estado_civil',
        'sexo',
        'hijos',
        'agencia_id',
        'correo_electronico',

        'numero_expediente',
        'numero_expediente_2',
        'codigo_expediente',
        'codigo_expediente_2',
        'asesor_id',
        'promotor_id',
        'central_riesgo',
        'calificacion',
        'canal_referencia',

        'monto_maximo',
        'notas',
        'reportar_equifax',

        'direccion',
        'departamento_id',
        'provincia_id',
        'distrito_id',
        'referencia_direccion',
        'telefonos',

        'imagen_dni',
        'observaciones',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }
}
