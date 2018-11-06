<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">

        @include('include.autocompleteRestaurants')

        <title>Administración</title>
        {{ HTML::style('css/boobstrap/bootstrap.css') }}
        {{ HTML::style('css/boobstrap/font-awesome.min.css') }}
        {{ HTML::style('css/news/general.css') }}
        {{ HTML::script('js/boobstrap/bootstrap.js') }}
        {{ HTML::script('js/jquery-1.12.4.js') }}
        {{ HTML::style('css/jquery-ui.css') }}
        {{ HTML::script('js/jquery-ui-1.11.4.min.js', array("type" => "text/javascript")) }}
        {{ HTML::script('js/jquery.datetimepicker.js') }}
        {{ HTML::style('css/jquery.datetimepicker.css') }}
        {{HTML::script('js/bootstrap-timepicker.js')}}
        {{HTML::script('js/wickedpicker.js')}}
        {{HTML::style('css/wickedpicker.css')}}
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{ HTML::style('css/admin.css') }}
        <script type="text/javascript">
            $(document).ready(function() {
                /*$(function () {
                    $('#datetimepicker3').datetimepicker({
                        pickDate: false
                    });
                });*/
                var options = {
                    //now: "12:35", //hh:mm 24 hour format only, defaults to current time
                    twentyFour: true,  //Display 24 hour format, defaults to false
                    upArrow: 'wickedpicker__controls__control-up',  //The up arrow class selector to use, for custom CSS
                    downArrow: 'wickedpicker__controls__control-down', //The down arrow class selector to use, for custom CSS
                    close: 'wickedpicker__close', //The close class selector to use, for custom CSS
                    hoverState: 'hover-state', //The hover state class to use, for custom CSS
                    title: 'Hora', //The Wickedpicker's title,
                    showSeconds: false, //Whether or not to show seconds,
                    secondsInterval: 1, //Change interval for seconds, defaults to 1,
                    minutesInterval: 1, //Change interval for minutes, defaults to 1
                    beforeShow: null, //A function to be called before the Wickedpicker is shown
                    show: null, //A function to be called when the Wickedpicker is shown
                    clearable: false, //Make the picker's input clearable (has clickable "x")
                };

                $('.timepickerC').wickedpicker(options);
                $('.timepickerA').wickedpicker(options);
                $('.timepickerC2').wickedpicker(options);
                $('.timepickerA2').wickedpicker(options);
            });
        </script>        
    </head>
    <body>
        <nav class="navbar navbar-default" role="navigation" style="margin-bottom: 0px;">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse"
                        data-target=".navbar-ex1-collapse">
                    <i class="fa fa-bars fa-2x"></i>
                </button>
                <a class="navbar-brand" href="{{ URL::to('admin/') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-cog fa-lg"></i></span> Administrar <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ URL::to('admin') }}">Administración principal</a></li>
                            <li><a href="{{ URL::to('admin/restaurant/list_schedules') }}">Lista de Horarios</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ URL::to('admin/restaurant/list') }}">Lista Restaurantes</a></li>
                            <li><a href="{{ URL::to('admin/restaurant/create') }}">Agregar Restaurante</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Lista de Productos</a></li>
                            <li><a href="#">Agregar Producto</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Lista de Tags</a></li>
                            <li><a href="#">Agregar Tag</a></li>
                        </ul>
                    </li>
                </ul>
                
    <ul class="nav navbar-nav navbar-right">
		@if(Auth::check())
			<li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
			<li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li>
		@else
			<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
		@endif
    </ul>
            </div>
        </nav>
        <main>
            <section class="module parallax parallax-2">
                @include('partials.alerts.error')

                @if(Session::has('flash_message'))
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    {{ Session::get('flash_message') }}
                </div>
                @endif
                <div id="contContent">
                    @yield('content')
                </div>
            </section>
        </main>
        <footer>
            <div class="container-fluid white_content" style="background-color: #1a1a1a; padding: 25px;">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                            <p style="color: #5f5f5f;">Acerca de nosotros</p>
                            <ul>
                                <a href="#"><li>Contacto</li></a>
                                <a href="#"><li>Prensa</li></a>
                                <a href="#"><li>Terminos y condiciones</li></a>
                                <a href="#"><li>Privacidad</li></a>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                            <p style="color: #5f5f5f;">Si tienes dificultades en el sitio:</p>
                            <ul>
                                <a href="#"><li>Preguntas frecuentes</li></a>
                                <a href="#"><li>Mapa del sitio</li></a>
                            </ul>
                        </div>
                        <!--<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <a href="#" class="btn btn_fb btn-block">Estamos en Facebook</a>
                                <a href="#" class="btn btn_go btn-block">Estamos en Google +</a>
                        </div>-->
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>	 