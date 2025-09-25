<!DOCTYPE html>
<html lang="es">

<head>
    <title>CredyApp</title>
    <meta charset="UTF-8">

    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#006FAA">
    <meta name="MobileOptimized" content="width">
    <meta name="HandheldFriendly" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/index/icono_1024.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/index/icono_1024.png') }}">
    <link rel="apple-touch-startup-image" href="{{ asset('images/index/icono_1024.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/bootstrap/bootstrap.min.css') }}" type="text/css">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{ asset('vendors/fontawesome/all.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('css/index/styles.css') }}" type="text/css">

</head>

<body>

    <div class="container-all d-flex flex-sm-row flex-column" id="general-content">

        <div class="container-picture mr-auto p-2">
            <div class="container-logo">
                <img class="pic-logo" src="{{ asset('images/general/logo-blanco.svg') }}" alt="logo-empresa">
            </div>

            <img class="pic1" src="{{ asset('images/index/imagen-contenido.svg') }}" alt="imagen-contenido">
        </div>
        <div class="container-panel p-2">
            <span class="panel-title">
                <img class="pic2" src="{{ asset('images/index/login.svg') }}" alt="logo-login">
            </span>

            <div class="container-buttons">
                <a class="button b1" href="/login">
                    <span>SISTEMA</span> <i class="far fa-building fa-lg"></i>
                </a>

                <a class="button b3" href="https://credipymehuanca.com.pe:2096" target="_blank">
                    <span>CORREO</span> <i class="far fa-envelope"></i>
                </a>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
                .then(reg => console.log('Registro de SW exitoso', reg))
                .catch(err => console.warn('Error al tratar de registrar el sw', err))
        }
    </script>

</body>

</html>