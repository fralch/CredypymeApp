<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('vendors/bootstrap/bootstrap.min.css')}}" type="text/css">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{asset('vendors/fontawesome/all.css')}}" type="text/css">

    <link rel="stylesheet" href="{{asset('css/login/styles.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('css/normalize.css')}}" type="text/css">

    <link rel="icon" href="{{asset('images/icono.png')}}" type="image/png" />
    <script src="{{asset('vendors/sweetalert2/sweetalert2.js')}}"></script>

    <title>Login</title>
</head>

<body>

    <div class="navbar-header">
        <a class="navbar-brand" href="./"><img src="{{asset('images/general/logo-blanco.svg')}}" alt="Logo" id="logoPrincipal"></a>
    </div>
    <div class="container login-container" style="z-index:1;">
        <div class="row">
            <div class="col-md-6 login-form-1">
                <img src="{{asset('images/general/foto-usuario.svg')}}" id="imagenContenido" />
            </div>
            <div class="col-md-6 login-form-2">
                <form action="/usuario/validar" method="get">
                    <h3 class="register-heading">INICIAR SESIÓN</h3>
                    <div class="col register-content">
                        <div class="row form-group">
                            <img src="{{asset('images/login/userIcon.svg')}}" class="iconosLogin" />
                            <input type="text" name="usuario" class="form-control" placeholder="Usuario" spellcheck="false" id="txtUsuario" />
                        </div>
                        <div class="row form-group">
                            <img src="{{asset('images/login/passwordIcon.svg')}}" class="iconosLogin" />
                            <div class="input-group" id="show_hide_password">
                                <input type="password" name="clave" class="form-control" placeholder="Contraseña" spellcheck="false" id="txtContraseña" />
                                <div class="input-group-append">
                                    <span class="input-group-text input-password-hide" style="cursor: pointer;" id="btnVerContraseña">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row form-group">
                            <button type="button" class="btn btn-link" id="lnkOlvidasteContraseña">¿Olvidaste tu contraseña?</button>
                        </div> -->
                        <div class="row form-group">
                            <button type="submit" class="btn btn-primary" id="btnEntrar">INGRESAR</button>
                        </div>
                        <div class="row form-group">
                            <p class="copyright">&copy; 2025 Grupo Credipyme</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @if(Session::has('usuario_no_valido'))

    <script>
        Swal.fire({
            icon: 'error',
            title: '¡Ups!',
            text: '{{session("usuario_no_valido")}}',
            confirmButtonText: 'Aceptar',
        })
    </script>
    @endif

    <!-- Jquery-->
    <script src="{{asset('vendors/jquery/jquery.js') }}"></script>
    <!-- Scripts para Bootstrap-->
    <script src="{{asset('vendors/bootstrap/popper.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap/bootstrap.min.js')}}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{asset('js/login/scripts.js')}}"></script>

</body>

</html>