<?php

namespace App\Http\Controllers\Gth\Asistencias;

use App\Models\Gth\Asistencias\TokenAsistencia;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function validar_token(Request $request)
    {

        $token_u = $request->token;
        $token_s = TokenAsistencia::all()->last()['token'];

        if ($token_s == $token_u) {
            session(['asistencia_token' => TRUE]);
            return 'CORRECTO';
        } else {
            session(['asistencia_token' => FALSE]);
            return 'INCORRECTO';
        }
    }
}
