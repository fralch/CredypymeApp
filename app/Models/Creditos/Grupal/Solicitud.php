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
        'grupo_id',
        'asesor_id',
        'plazo',
        'periodo_pago',
        'tasa_interes',
        'tasa_retencion',

        'estado_id',

        'fecha_solicitud',
        'usuario_solicitud',
        'fecha_aprobacion',
        'usuario_aprobacion',
        'fecha_desaprobacion',
        'usuario_desaprobacion',
        'agencia_caja',
        'caja_id',

        'data_created',
        'data_updated',

        'created_at',
        'updated_at'
    ];

    // Relación con el modelo Usuario
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
    // Relación con el modelo Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }
}
