<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiSmsController extends Controller
{

    public function single_send($phone_number, $message)
    {
        // Recibe parámetros

        $phone_number = $phone_number;
        $message = $message;

        // Validaciones

        $phone_validation = $this->phone_validation($phone_number)->getData();
        if ($phone_validation->status != 'success') {
            return response()->json([
                'status' => 'error',
                'data' => $phone_validation->response
            ], 400);
        } else {
            $phone_number = $phone_validation->data;
        }

        $message_validation = $this->message_validation($message)->getData();
        if ($message_validation->status != 'success') {
            return response()->json([
                'status' => 'error',
                'data' => $message_validation->response
            ], 400);
        } else {
            $message = $message_validation->data;
        }

        // Construcción del formulario necesario

        $post_fields = array(
            'message' => $message,
            'tpoa' => '22435',
            'recipient' =>  array(
                array(
                    'msisdn' => '51' . $phone_number
                )
            )
        );

        // Inicia la petición

        $curl = curl_init();
        $auth_basic = base64_encode("soporte_ti@credipyme.com.pe:Dy2TUw7EErKiUYLYZodnO9fc7N7pVOXp");

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.labsmobile.com/json/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $auth_basic,
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        // Manejo de respuestas

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json(['status' => 'error', 'data' => $err], 400);
        } else {
            return response()->json(['status' => 'success', 'data' => $response], 201);
        }
    }

    public function message_validation($message)
    {
        $status = 'error';
        $response = '';
        $code = 400;

        if ($message !== null && $message !== "") {
            if (isset($message) && $message !== "") {
                if (!empty($message)) {
                    $message = trim($message);
                    if (strlen($message) <= 160) {
                        $status = 'success';
                        $response = 'Mensaje SMS validado.';
                        $code = 201;
                    } else {
                        $response = 'La cantidad de caracteres del mensaje no es correcta.';
                    }
                } else {
                    $response = 'El mensaje está vacío o es nulo.';
                }
            } else {
                $response = 'El mensaje está vacío o no está definido.';
            }
        } else {
            $response = 'El mensaje está vacío o es nulo.';
        }

        return response()->json([
            'status' => $status,
            'response' => $response,
            'data' => $message
        ], $code);
    }

    public function phone_validation($phone_number)
    {
        $status = 'error';
        $response = '';
        $code = 400;

        if ($phone_number !== null && $phone_number !== "") {
            if (isset($phone_number) && $phone_number !== "") {
                if (!empty($phone_number)) {
                    $phone_number = trim($phone_number);
                    if (strlen($phone_number) == 9) {
                        if (preg_match('/^\d+$/', $phone_number)) {
                            $status = 'success';
                            $response = 'Número SMS validado.';
                            $code = 201;
                        } else {
                            $response = 'El número es inválido.';
                        }
                    } else {
                        $response = 'La cantidad de caractéres del número no es correcta';
                    }
                } else {
                    $response = 'El número está vacío o es nulo.';
                }
            } else {
                $response = 'El número está vacío o no está definido.';
            }
        } else {
            $response = 'El número está vacío o es nulo.';
        }

        return response()->json([
            'status' => $status,
            'response' => $response,
            'data' => $phone_number
        ], $code);
    }
}
