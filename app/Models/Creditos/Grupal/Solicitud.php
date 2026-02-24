<?php

namespace App\Models\Creditos\Grupal;

use App\Models\General\Agencia;
use App\Models\Creditos\Clientes\Grupo;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'grupo_solicitudes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_id',
        'grupo_id',
        'asesor_id',
        'estado_id',

        'datos_creacion',
        'datos_actualizacion',

        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'datos_creacion' => 'array',
        'datos_actualizacion' => 'array',
    ];


    // Relación con el modelo Usuario
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
    // Relación con el modelo Sede
    public function agencia()
    {
        return $this->belongsTo(Agencia::class, 'agencia_id');
    }
    // Relación con el modelo Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
