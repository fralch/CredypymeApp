<?php

namespace App\Models\Creditos\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoVoucher extends Model
{
    use HasFactory;
    protected $table = 'caja_pago_vouchers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'credito_id',
        'agencia_id',
        'caja_id',
        'numero_cuota',
        'datos_voucher',
        'datos_creacion',
        'created_at',
        'updated_at'
    ];
}
