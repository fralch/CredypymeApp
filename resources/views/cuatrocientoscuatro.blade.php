<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="icon" href="{{asset('images/icono.png')}}" type="image/png" />
</head>

<body>

    <div class="stars">
        <div class="custom-navbar">
            <div class="brand-logo">
                <img src="{{asset('images/error-img/logoBlanco.svg')}}" width="150px">
            </div>
        </div>
        <div class="central-body">
            <img class="image-404" src="{{asset('images/error-img/imagenerror.svg')}}" width="300px">
            <p class="message1">{{$mensajeTitulo}}</p>
            <p class="message2">{{$mensajeContenido}}</p>
        </div>
        <div class="objects">
            <img class="object_rocket" src="{{asset('images/error-img/rocket.svg')}}" width="40px">
            <div class="earth-moon">
                <img class="object_earth" src="{{asset('images/error-img/earth.svg')}}" width="100px">
                <img class="object_moon" src="{{asset('images/error-img/moon.svg')}}" width="80px">
            </div>
            <div class="box_astronaut">
                <img class="object_astronaut" src="{{asset('images/error-img/astronaut.svg')}}" width="140px">
            </div>
        </div>
        <div class="glowing_stars">
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
            <div class="star"></div>
        </div>

    </div>



    <style type="text/css">
        @-moz-keyframes rocket-movement {
            100% {
                -moz-transform: translate(1200px, -600px);
            }
        }

        @-webkit-keyframes rocket-movement {
            100% {
                -webkit-transform: translate(1200px, -600px);
            }
        }

        @keyframes rocket-movement {
            100% {
                transform: translate(1200px, -600px);
            }
        }

        @-moz-keyframes spin-earth {
            100% {
                -moz-transform: rotate(-360deg);
                transition: transform 20s;
            }
        }

        @-webkit-keyframes spin-earth {
            100% {
                -webkit-transform: rotate(-360deg);
                transition: transform 20s;
            }
        }

        @keyframes spin-earth {
            100% {
                -webkit-transform: rotate(-360deg);
                transform: rotate(-360deg);
                transition: transform 20s;
            }
        }

        @-moz-keyframes move-astronaut {
            100% {
                -moz-transform: translate(-160px, -160px);
            }
        }

        @-webkit-keyframes move-astronaut {
            100% {
                -webkit-transform: translate(-160px, -160px);
            }
        }

        @keyframes move-astronaut {
            100% {
                -webkit-transform: translate(-160px, -160px);
                transform: translate(-160px, -160px);
            }
        }

        @-moz-keyframes rotate-astronaut {
            100% {
                -moz-transform: rotate(-720deg);
            }
        }

        @-webkit-keyframes rotate-astronaut {
            100% {
                -webkit-transform: rotate(-720deg);
            }
        }

        @keyframes rotate-astronaut {
            100% {
                -webkit-transform: rotate(-720deg);
                transform: rotate(-720deg);
            }
        }

        @-moz-keyframes glow-star {
            40% {
                -moz-opacity: 0.3;
            }

            90%,
            100% {
                -moz-opacity: 1;
                -moz-transform: scale(1.2);
            }
        }

        @-webkit-keyframes glow-star {
            40% {
                -webkit-opacity: 0.3;
            }

            90%,
            100% {
                -webkit-opacity: 1;
                -webkit-transform: scale(1.2);
            }
        }

        @keyframes glow-star {
            40% {
                -webkit-opacity: 0.3;
                opacity: 0.3;
            }

            90%,
            100% {
                -webkit-opacity: 1;
                opacity: 1;
                -webkit-transform: scale(1.2);
                transform: scale(1.2);
                border-radius: 999999px;
            }
        }

        .spin-earth-on-hover {

            transition: ease 200s !important;
            transform: rotate(-3600deg) !important;
        }

        html,
        body {
            margin: 0;
            width: 100%;
            height: 100%;
            font-family: 'Dosis', sans-serif;
            font-weight: 300;
            background: rgba(61, 110, 209, 0.99);
            background: -moz-linear-gradient(left, rgba(61, 110, 209, 0.99) 0%, rgba(37, 128, 221, 0.99) 20%, rgba(0, 156, 240, 1) 50%, rgba(34, 130, 223, 1) 78%, rgba(61, 110, 209, 1) 100%);
            background: -webkit-gradient(left top, right top, color-stop(0%, rgba(61, 110, 209, 0.99)), color-stop(20%, rgba(37, 128, 221, 0.99)), color-stop(50%, rgba(0, 156, 240, 1)), color-stop(78%, rgba(34, 130, 223, 1)), color-stop(100%, rgba(61, 110, 209, 1)));
            background: -webkit-linear-gradient(left, rgba(61, 110, 209, 0.99) 0%, rgba(37, 128, 221, 0.99) 20%, rgba(0, 156, 240, 1) 50%, rgba(34, 130, 223, 1) 78%, rgba(61, 110, 209, 1) 100%);
            background: -o-linear-gradient(left, rgba(61, 110, 209, 0.99) 0%, rgba(37, 128, 221, 0.99) 20%, rgba(0, 156, 240, 1) 50%, rgba(34, 130, 223, 1) 78%, rgba(61, 110, 209, 1) 100%);
            background: -ms-linear-gradient(left, rgba(61, 110, 209, 0.99) 0%, rgba(37, 128, 221, 0.99) 20%, rgba(0, 156, 240, 1) 50%, rgba(34, 130, 223, 1) 78%, rgba(61, 110, 209, 1) 100%);
            background: linear-gradient(to right, rgba(61, 110, 209, 0.99) 0%, rgba(37, 128, 221, 0.99) 20%, rgba(0, 156, 240, 1) 50%, rgba(34, 130, 223, 1) 78%, rgba(61, 110, 209, 1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#3d6ed1', endColorstr='#3d6ed1', GradientType=1);
            -webkit-user-select: none;
            /* Safari 3.1+ */
            -moz-user-select: none;
            /* Firefox 2+ */
            -ms-user-select: none;
            /* IE 10+ */
            user-select: none;
            /* Standard syntax */
        }


        .custom-navbar {
            padding-top: 15px;
        }

        .brand-logo {
            margin-left: 25px;
            margin-top: 5px;
            display: inline-block;
        }


        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            /*    overflow: hidden;*/
            display: flex;
            align-items: center;
        }

        .central-body {
            /*    width: 100%;*/
            padding: 17% 5% 10% 5%;
            text-align: center;
        }

        .objects img {
            z-index: 90;
            pointer-events: none;
        }

        .object_rocket {
            z-index: 95;
            position: absolute;
            transform: translateX(-50px);
            top: 75%;
            pointer-events: none;
            animation: rocket-movement 200s linear infinite both running;
        }

        .object_earth {
            position: absolute;
            top: 20%;
            left: 15%;
            z-index: 90;
            /*    animation: spin-earth 100s infinite linear both;*/
        }

        .object_moon {
            position: absolute;
            top: 12%;
            left: 25%;
            /*
    transform: rotate(0deg);
    transition: transform ease-in 99999999999s;
*/
        }

        .object_astronaut {
            animation: rotate-astronaut 200s infinite linear both alternate;
        }

        .box_astronaut {
            z-index: 110 !important;
            position: absolute;
            top: 60%;
            right: 20%;
            will-change: transform;
            animation: move-astronaut 50s infinite linear both alternate;
        }

        .image-404 {
            position: relative;
            z-index: 100;
            pointer-events: none;
        }

        .message1 {

            width: 30%;
            margin-left: 35%;
            color: white;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            font-size: 15px;
        }

        .message2 {

            width: 50%;
            margin-left: 25%;
            color: white;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            font-size: 15px;
        }

        .stars {
            background: url(../../images/error-img/overlay_stars.svg);
            background-repeat: repeat;
            background-size: contain;
            background-position: left top;
        }

        .glowing_stars .star {
            position: absolute;
            border-radius: 100%;
            background-color: #fff;
            width: 3px;
            height: 3px;
            opacity: 0.3;
            will-change: opacity;
        }

        .glowing_stars .star:nth-child(1) {
            top: 80%;
            left: 25%;
            animation: glow-star 2s infinite ease-in-out alternate 1s;
        }

        .glowing_stars .star:nth-child(2) {
            top: 20%;
            left: 40%;
            animation: glow-star 2s infinite ease-in-out alternate 3s;
        }

        .glowing_stars .star:nth-child(3) {
            top: 25%;
            left: 25%;
            animation: glow-star 2s infinite ease-in-out alternate 5s;
        }

        .glowing_stars .star:nth-child(4) {
            top: 75%;
            left: 80%;
            animation: glow-star 2s infinite ease-in-out alternate 7s;
        }

        .glowing_stars .star:nth-child(5) {
            top: 90%;
            left: 50%;
            animation: glow-star 2s infinite ease-in-out alternate 9s;
        }

        @media screen and (max-width: 900px) {

            .custom-navbar {
                text-align: center;
            }

            .brand-logo img {
                width: 120px;
            }

            .box_astronaut {
                top: 75%;
            }

            .object_astronaut {
                width: 100px;
            }

            .central-body {
                padding-top: 25%;
            }

            .image-404 {
                width: 30%;
            }

            .message1 .message2 {

                width: 70%;
                margin-left: 15%;

            }


        }
    </style>

</body>

</html>
