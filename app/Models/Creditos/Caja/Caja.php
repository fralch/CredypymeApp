<?php

namespace App\Models\Creditos\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Caja extends Model
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    use HasFactory;
    protected $table = 'caja_registros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dni',
        'agencia_id',
        'comentario_apertura',
        'comentario_cierre',
        'monto_apertura',
        'monto_cierre_ingresos',
        'monto_cierre_egresos',
        'datos_apertura',
        'datos_cierre',

        'created_at',
        'updated_at'
    ];

    public $timestamps = true;
    
}
