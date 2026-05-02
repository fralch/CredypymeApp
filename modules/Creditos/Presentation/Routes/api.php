<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\Creditos\Presentation\Controllers\Apis\ApiClienteController;
use Modules\Creditos\Presentation\Controllers\Apis\ApiExternoController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::GET('/acceso/cliente/{dni}/{clave}', [ApiClienteController::class, 'acceder']);

Route::GET('/cliente/datos_personales/{agencia_id}/{cliente_id}', [ApiClienteController::class, 'datos_personales']);
Route::POST('/cliente/datos_personales', [ApiClienteController::class, 'actualizar']);

Route::GET('/cliente/historial_creditos/{agencia_id}/{cliente_id}', [ApiClienteController::class, 'historial_creditos']);
Route::GET('/cliente/historial_creditos/detalle/{agencia_id}/{credito_id}', [ApiClienteController::class, 'detalle_pagos']);

// APIS EXTERNAS

Route::GET('/caj/desembolso_externo/buscar', [ApiExternoController::class, 'buscar_externo']);

Route::GET('/caj/cobranza_externa/informacion_credito', [ApiExternoController::class, 'informacion_credito']);
Route::POST('/caj/cobranza_externa/pagar', [ApiExternoController::class, 'pagar']);
Route::POST('/caj/cobranza_externa/cancelar', [ApiExternoController::class, 'cancelar']);

Route::GET('/caj/pago_cuotas/listar', [ApiExternoController::class, 'listar_pagos_cuotas']);
Route::GET('/caj/pago_moras/listar', [ApiExternoController::class, 'listar_pagos_moras']);
Route::GET('/caj/pago_notificaciones/listar', [ApiExternoController::class, 'listar_pagos_notificaciones']);

Route::GET('/inv/meta_externa/buscar', [ApiExternoController::class, 'buscar_inversiones']);
Route::GET('/inv/meta_externa/listar_recursos', [ApiExternoController::class, 'listar_recursos_inversion']);
Route::POST('/inv/meta_externa/abonar', [ApiExternoController::class, 'abonar_inversion']);

Route::GET('/cli/listado_externa/buscar', [ApiExternoController::class, 'buscar_cliente']);
Route::GET('/cli/listado_externa/datos_cliente', [ApiExternoController::class, 'datos_cliente']);
Route::GET('/cli/listado_externa/verificar', [ApiExternoController::class, 'verificar_cliente']);

Route::GET('/cli/listado_externa/buscar_parientes_avales', [ApiExternoController::class, 'buscar_parientes_avales']);

Route::GET('/cli/listado_externa/datos_pariente_aval', [ApiExternoController::class, 'datos_pariente_aval']);
