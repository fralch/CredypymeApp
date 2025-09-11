<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Creditos\Auditoria\CarritoDetalleAUD;

class CarritoDetalle extends Model
{
    protected $table = 'credito_carrito_detalles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_carrito',
        'carrito_id',
        'credito_id',
        'pago_por_cuota',
        'pago_cantidad_cuota',
        'pago_cuota_monto',
        'pago_por_monto',
        'pago_monto',
        'pago_mora',
        'pago_mora_monto',
        'pago_notificaciones',
        'pago_notificaciones_monto',
        'estado',
        'motivo_anulacion',
        'total_cobro',
        'ticket',
        'telefono_envio',
        'fecha_cobro',
        'fecha_pago',
        'agencia_caja',
        'caja_pago',
        'voucher_id',
        'modo_envio',
        'envio_voucher',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'

    ];

    protected $casts = [
        'pago_mora' => 'boolean',
        'pago_notificaciones' => 'boolean',
    ];


    // Evento para detectar actualizaciones
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::updating(function ($model) {

    //         foreach ($model->getDirty() as $attribute => $value) {
    //             CarritoDetalleAUD::on($model->getConnectionName())->create([
    //                 'usuario_id' => session('usuario_dni'),
    //                 'registro_id' => $model->id,
    //                 'campo' => $attribute,
    //                 'valor_anterior' => $model->getOriginal($attribute),
    //                 'valor_nuevo' => $value,
    //             ]);
    //         }
    //     });
    // }
}
