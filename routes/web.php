<?php

use Illuminate\Support\Facades\Route;

//--------GENERAL-------------------------------------------------------------------
//-------------------SESIONES-------------------------------------------------------

use App\Http\Controllers\General\SesionController;
use App\Http\Controllers\Gth\Usuarios\UsuarioController;

//-----------------------------WELCOME-LOGIN-HOME------------------------------------

Route::get('/', [UsuarioController::class, 'welcome']);
Route::get('/login', [UsuarioController::class, 'login']);
Route::get('/home', [UsuarioController::class, 'home']);
Route::get('usuario/validar', [UsuarioController::class, 'validarUsuario']);

Route::get('/', [UsuarioController::class, 'welcome'])->name('welcome');
Route::get('/login', [UsuarioController::class, 'login'])->name('login');
Route::get('/home', [UsuarioController::class, 'home'])->name('home');
Route::get('usuario/validar', [UsuarioController::class, 'validarUsuario'])->name('validarUsuario');
Route::get('usuario/actualizar_clave/{dni}', [UsuarioController::class, 'actualizar_clave'])->name('actualizar_clave');
Route::post('usuario/actualizar_clave/guardar', [UsuarioController::class, 'guardando_nueva_clave'])->name('guardando_nueva_clave');
Route::post('usuario/validar/api', [UsuarioController::class, 'validarUsuario_api']);
Route::get('/cerrar_sesion', [UsuarioController::class, 'cerrar_sesion'])->name('cerrar_sesion');
Route::get('/online', [SesionController::class, 'usuarios_online'])->name('usuarios_online');
Route::get('/logout', [SesionController::class, 'cerrar_sesion'])->name('logout');
Route::view('/cuatrocientoscuatro', 'cuatrocientoscuatro', ['mensajeTitulo' => '¡Ups!', 'mensajeContenido' => 'En construcción']);

Route::post('/comprobar_sesion', [UsuarioController::class, 'ComprobarSesion'])->name('comprobar_sesion');


Route::post('/home/mora_resumen', [UsuarioController::class, 'mora_resumen'])->name('home.mora_resumen');
// -----------------------------------------------------------------------------------

//-----------------------------------------APIS-------------------------------------------

Route::post(
    '/usuario/listar_por_cargos_agencia',
    [UsuarioController::class, 'listar_por_cargos_agencia']
)->name('usuarios.listar_por_cargos_agencia');

//--------------------------------------------------------------------------------------

Route::prefix('general')
    ->group(base_path('routes/general.php'));

Route::prefix('creditos')
    ->group(base_path('routes/creditos.php'));

Route::prefix('gth')
    ->group(base_path('routes/gth.php'));

Route::prefix('logistica')
    ->group(base_path('routes/logistica.php'));
