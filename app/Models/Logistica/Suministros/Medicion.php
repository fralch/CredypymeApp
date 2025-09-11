<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;

class Medicion extends Model
{
    protected $table = 'logistica_mediciones';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'medicion',
        'escala',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'escala' => 'float',
    ];
}
