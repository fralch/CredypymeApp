<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion_financiera extends Model
{
    use HasFactory;
    protected $table = 'evaluacion_financiera_registros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cliente_id',
        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
