<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ef_pasivo_corriente extends Model
{
    use HasFactory;
    protected $table = 'evaluacion_financiera_pasivo_corriente';
    protected $primaryKey = 'id';

    protected $fillable = [
        'evaluacion_id',
        'adelanto_proveedores',
        'otros',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
