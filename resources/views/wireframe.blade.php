<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Wireframe</title>

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <a href="#wireframe-2" onclick="changeWireframe(this);">
            <img id="wireframe-1" src="{{ asset('wireframe-1.jpg') }}" style="display:block;">
        </a>
        <a href="#wireframe-1" onclick="changeWireframe(this);">
            <img id="wireframe-2" src="{{ asset('wireframe-2.jpg') }}" style="display:none;">
        </a>
        <script>
            function changeWireframe(el,$event){
                let id = el.getAttribute('href');
                el.querySelector('img').style.display = "none";
                document.querySelector(`${id}`).style.display = "block";
            }
        </script>
    </body>
</html>
