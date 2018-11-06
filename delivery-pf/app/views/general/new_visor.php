<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Delivery Pidafacil</title>

        <!-- Bootstrap Core CSS -->
        {{ HTML::style('css/bootstrap.min.css') }}
        <!-- Custom CSS -->
        {{ HTML::style('css/simple-sidebar.css') }}
        {{ HTML::style('css/boobstrap/bootstrap.css') }}
        {{ HTML::style('css/boobstrap/font-awesome.min.css') }}
        {{ HTML::style('css/news/general.css') }}
        {{ HTML::style('css/app.css') }}

    </head>
    <body>
        <div id="wrapper">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <li class="sidebar-brand">
                        <h4 style="color:white;">Visor Delivery</h4>
                    </li>
                    <li>
                        <a href="/delivery_pidafacil">Principal</a>
                    </li>
                    <li>
                        <a href="#">Reportes</a>
                    </li>
                    <li>
                        <a href="#">Overview</a>
                    </li>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
            <!-- /#page-content-wrapper -->
        </div>
        <!-- /#wrapper -->
        <!-- jQuery -->
        {{HTML::script('js/jquery.js')}}
        <!-- Bootstrap Core JavaScript -->
        {{HTML::script('js/bootstrap.min.js')}}
        {{ HTML::script('js/boobstrap/bootstrap.js') }}
        {{HTML::script('http://api.html5media.info/1.1.5/html5media.min.js')}}
        <!-- Menu Toggle Script -->
        <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        </script>

    </body>

</html>
