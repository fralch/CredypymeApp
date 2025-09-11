<?php

namespace App\Models\Creditos\Cuenta\Records;

use Illuminate\Database\Eloquent\Model;

class BancoRecord extends Model
{
    protected $connection = 'records';
    protected $table = 'banco_records';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'banco_id',
        'agencia_id',
        'monto_inicial',
        'monto_final',
        'fecha',
        'cierre_id'
    ];
}
