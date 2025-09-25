<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

define('API_URL', 'https://apiwsp.factiliza.com/v1/message');
define('API_INSTANCE', 'NTE5NDEzODA1MDc=');
define('API_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzOTU0NSIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.SneWgmC2ux6Gm7-n-hFkc3obnKSoNYnzey3MJUe7aIE');

class ApiWhatsAppController extends Controller
{
    public function text_send($phone_number, $message)
    {
        // Recibe parámetros

        $phone_number = $phone_number;
        $message = $message;

        // Validaciones

        $phone_validation = $this->phone_validation($phone_number)->getData();

        if ($phone_validation->status != 'success') {
            return  $phone_validation;
        }

        $message_validation = $this->message_validation($message)->getData();
        if ($message_validation->status != 'success') {
            return  $message_validation;
        }

        // Construcción del formulario necesario

        $post_fields = array(
            'number' => (string)'51' . $phone_number,
            'text' => (string)$message
        );

        // Inicia la petición

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => API_URL . "/sendtext/" . API_INSTANCE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . API_TOKEN,
                "Content-Type: application/json"
            ],
        ]);

        // Manejo de respuestas

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json([
                'status' => 'error',
                'data' => 'Error al realizar la solicitud: ' . $err
            ], 400);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => $response
            ], 201);
        }
    }

    public function text_image_send($phone_number, $message, $image_path)
    {
        // Recibe parámetros

        $phone_number = $phone_number;
        $message = $message;

        // Validaciones

        $phone_validation = $this->phone_validation($phone_number)->getData();
        if ($phone_validation->status != 'success') {
            return $phone_validation;
        }

        $message_validation = $this->message_validation($message)->getData();
        if ($message_validation->status != 'success') {
            return $message_validation;
        }

        $image_validation = $this->image_validation($image_path)->getData();
        if ($image_validation->status != 'success') {
            return $image_validation;
        }

        // Inicia la petición

        $file_extension = File::extension($image_path);
        $file_name = File::name($image_path);
        $file_name .= '.' . $file_extension;

        $enviroment = getenv('APP_ENV');
        if ($enviroment == 'production') {
            $file_media = asset('temp_files/' . $file_name);
        } else if ($enviroment == 'development') {
            $file_media = base64_encode(File::get($image_path));
        }

        $post_fields = array(
            'number' => '51' . $phone_number,
            'mediatype' => 'image',
            'media' => $file_media,
            'filename' => $file_name,
            'caption' => $message
        );

        $curl = curl_init();

        // Inicia la petición
        curl_setopt_array($curl, [
            CURLOPT_URL => API_URL . "/sendmedia/" . API_INSTANCE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . API_TOKEN,
                "Content-Type: application/json"
            ],
        ]);

        // Manejo de respuestas

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json([
                'status' => 'error',
                'data' => 'Error al realizar la solicitud: ' . $err
            ], 400);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => $response
            ], 201);
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
                    if (strlen($message) <= 600) {
                        $status = 'success';
                        $response = '¡Validación exitosa!';
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
                            $response = '¡Validación exitosa!';
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

    public function image_validation($file_path)
    {
        $status = 'error';
        $response = '';
        $code = 400;
        $extensions  = ['jpg', 'png', 'jpeg'];

        if ($file_path !== null && $file_path !== "") {
            if (isset($file_path) && $file_path !== "") {
                if (!empty($file_path)) {
                    $file_path = trim($file_path);
                    if (file_exists($file_path)) {
                        $file_extension = File::extension($file_path);
                        if (in_array($file_extension, $extensions)) {
                            $status = 'success';
                            $response = '¡Validación exitosa!';
                            $code = 201;
                        } else {
                            $response = 'El tipo de archivo no es válido.';
                        }
                    } else {
                        $response = 'El archivo no existe en la ruta especificada.';
                    }
                } else {
                    $response = 'La ruta del archivo está vácia o es nula.';
                }
            } else {
                $response = 'La ruta del archivo está vácia o no está definida.';
            }
        } else {
            $response = 'La ruta del archivo está vácia o es nula.';
        }

        return response()->json([
            'status' => $status,
            'response' => $response,
            'data' => $file_path
        ], $code);
    }
}
