<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <!-- Icono -->
    <link rel="icon" href="{{ asset('images/icono.png') }}" type="image/png" />

    <!-- Para Bootstrap -->

    <!-- Para Fontawesome -->
    <link rel="stylesheet" href="{{ asset('vendors/fontawesome/all.css') }}">

    <!-- Estilos DataTables -->
    <link rel="stylesheet" href="{{ asset('vendors/datatables/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/datatables/dataTables.select.min.css') }}">

    <!-- Estilos propios -->
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    <!-- Sweet Alert -->
    <script src="{{ asset('vendors/sweetalert2/sweetalert2.js') }}"></script>


    @vite('resources/css/app.css')
    @routes
    @inertiaHead

</head>

<body id="page-top">

    @inertia

    <!-- Jquery-->
    <script src="{{ asset('vendors/jquery/jquery.js') }}"></script>

    <!-- Jquery print-->
    <script src="{{ asset('vendors/jquery/jQuery.print.js') }}"></script>
    <!-- Webcam JS-->
    <script src="{{ asset('vendors/webcam/webcam.js') }}"></script>
    <!-- Pdf-->
    <script src="{{ asset('vendors/pdf/pdf.js') }}"></script>
    <script src="{{ asset('vendors/pdf/b64decode.js') }}"></script>
    <!-- Fechas-->
    <script src="{{ asset('vendors/moment/moment.min.js') }}"></script>
    <!-- Scripts para Bootstrap-->
    <script src="{{ asset('vendors/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>

    <!-- Scripts para DataTables-->
    <script src="{{ asset('vendors/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/Buttons-1.6.5/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/Buttons-1.6.5/js/buttons.html5.min.js') }}"></script>

    <script src="{{ asset('vendors/scrollTo/scrollTo.min.js') }}"></script>

    <script>
        window.addEventListener("popstate", function() {
            alert("Acción BLOQUEADA: la página actual se recargará.");
            window.location.reload();
        });
    </script>

    @vite('resources/js/app.js')

</body>

</html>