<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/bootstrap/bootstrap.min.css') }}" type="text/css">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{ asset('vendors/fontawesome/all.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/home/styles.css') }}" type="text/css">
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png" />

    <title>Home</title>
    <script src="{{ asset('vendors/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('vendors/jquery/jquery.js') }}"></script>
</head>

<body>
    <?php

    if (!empty($mensaje)) {
    ?>
        <script>
            if ('<?= $mensaje ?>' == "ACEPTADO") {
                Swal.fire({
                    icon: 'success',
                    title: 'Bienvenido',
                    text: '<?= session('nombres') ?>',
                    customClass: {
                        title: 'title-swal',
                        text: 'text-swal'
                    }
                })
            } else if ('<?= $mensaje ?>' == "RECHAZADO") {
                Swal.fire({
                    icon: 'error',
                    title: '¡Ups!',
                    text: 'Usted no está autorizado a este módulo',
                    confirmButtonText: 'Aceptar',
                })
            }
        </script>
    <?php
    }
    ?>


    <header id="header" class="header">
        <div class="top-left">
            <div class="navbar-header">
                <a class="navbar-brand" href="/home"><img class="logo-principal" src="/images/general/logo-blanco.svg" alt="Logo"></a>
            </div>
        </div>

        <div class="btn-group menu-button" role="group">
            <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item submenu-button" href="{{ route('gen.index') }}">GENERAL</a>
            </div>
        </div>
    </header>

    <div class="content" id='content_Principal' style='display:block'>
        <div class="btn-group">
            <a class="btn" href="{{ route('cre.index') }}">
                <div class="boton color-creditos" id="btnCreditos">CRÉDITOS</div>
                <span class="caret"></span>
            </a>
        </div>

        <div class="btn-group">
            <a class="btn dropdown-toggle" href="{{ route('gth.index') }}">
                <div class="boton color-gth" id="btnGth">GTH</div>
                <span class="caret"></span>
            </a>
        </div>
        <div class="btn-group">
            <a class="btn dropdown-toggle" href="{{ route('log.index') }}">
                <div class="boton color-logistica" id="btnGth">LOGÍSTICA</div>
                <span class="caret"></span>
            </a>
        </div>
    </div>

    <span class="version">

        <?php
        foreach ($version as $item) {
        ?>
            Versión: {{ $item->numeroVersion }}
        <?php
        }
        ?>
        <span>

            <button class="btn btn-primary btn-close" onclick="cerrarSesion()">
                <i class="fas fa-power-off"></i>
            </button>


            <script type="text/javascript">
                function cerrarSesion() {
                    Swal.fire({
                        title: 'CERRAR SESIÓN',
                        text: "Su sesión será cerrada. ¿Desea continuar?",
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Aceptar',
                        position: 'bottom-end',
                        customClass: {
                            title: 'title-swal',
                            text: 'text-swal'
                        }
                    }).then((result) => {

                        if (result.value == true) {
                            let timerInterval
                            Swal.fire({
                                title: 'Cerrando sesión!',
                                html: 'Su sesión se esta cerrando en  <b></b> milisegundos',
                                timer: 500,
                                timerProgressBar: true,
                                onBeforeOpen: () => {
                                    Swal.showLoading()
                                    timerInterval = setInterval(() => {
                                        const content = Swal.getContent()
                                        if (content) {
                                            const b = content.querySelector('b')
                                            if (b) {
                                                b.textContent = Swal.getTimerLeft()
                                            }
                                        }
                                    }, 100)
                                },
                                onClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                window.location.href = "{{ route('logout') }}";
                            })

                        }

                    })

                }
            </script>

            <!-- Scripts para Bootstrap-->
            <script src="{{ asset('vendors/bootstrap/popper.min.js') }}"></script>
            <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
</body>

</html>