<?php

namespace App\Models\Gth\Usuarios;

use Illuminate\Database\Eloquent\Model;
use App\Models\Creditos\Credito\Credito;

class Usuario extends Model
{
    protected $primaryKey = 'dni';
    protected $fillable = [
        'dni',
        'usuario',
        'clave',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'sexo',
        'direccion',
        'fecha_nacimiento',
        'telefono',
        'correo_corporativo',
        'cargo_id',
        'agencia_id',
        'habilitado',
        'actualizo_clave',
        'distrito_id',
        'provincia_id',
        'departamento_id',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    // public function creditos()
    // {
    //     return $this->hasMany(Credito::class);
    // }

    protected $appends = ['nombre_completo'];

    public function getNombreCompletoAttribute()
    {
        return "{$this->apellido_paterno} {$this->apellido_materno} {$this->nombres}";
    }
}
