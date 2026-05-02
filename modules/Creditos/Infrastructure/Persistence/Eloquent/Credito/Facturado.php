<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturado extends Model
{
    use HasFactory;
    protected $table = 'credito_facturados';
    protected $primaryKey = 'id';
    protected $fillable = [
        'desembolso_id',
        'agencia_id',
        'tipo_comprobante',
        'datos_comprobante',

        'nueva_empresa',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
