<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagina en construcción</title>
    {{ HTML::style('css/boobstrap/bootstrap.css') }}
    <style>
        body
        {
            background-image: url('images/background.jpg');
        }
        h1
        {
            text-align: center;
            font-size: 7em;
            background-position: center;
            text-shadow: 2px 2px 2px #FFF, -2px -2px 2px #FFF;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-default" style="height: 200px; ">
    <div class="navbar-header">
        <a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse"></div>
</nav>
    <h1>Próximamente</h1>
</body>
</html>