<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'cargo',
        'descripcion',
        'jefatura',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
