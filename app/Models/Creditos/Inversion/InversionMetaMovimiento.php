<?php

namespace App\Models\Creditos\Inversion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InversionMetaMovimiento extends Model
{
    use HasFactory;

    protected $table = 'inversion_meta_movimientos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'inversion_id',
        'tipo',
        'monto',
        'comentario',
        'agencia_caja',
        'caja_id',
        'banco_id',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
