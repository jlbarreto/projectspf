<!DOCTYPE html>
<html lang="es">
    <head>
    	<meta charset="UTF-8">
            <title>PidaFacil Delivery</title>
    	@include('include.head')
    	{{ HTML::style('css/boobstrap/bootstrap.css') }}
    	{{-- HTML::style('css/boobstrap/font-awesome.css') --}}
    	{{ HTML::style('css/boobstrap/font-awesome.min.css') }}
    	{{ HTML::style('css/news/general.css') }}
    	{{ HTML::style('css/app.css') }}
    	{{ HTML::style('css/simple-sidebar.css') }}
    </head>
    <body>
    	<nav class="navbar navbar-default navbar-static-top" role="navigation">
      		<div class="navbar-header">
        		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          			<i class="fa fa-bars fa-2x"></i>
        		</button>
        		<a class="navbar-brand" href="{{ URL::to('delivery_pidafacil') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
      		</div>
      		<div class="collapse navbar-collapse navbar-ex1-collapse" style="padding:5px;">
                <ul class="nav navbar-nav navbar-right">
          			@if(Auth::check())
                        <li><a href="{{ URL::to('cart') }}" onclick="addC()"><i class="fa fa-cart-plus fa-lg"></i> Carrito de Compras</a></li>
    	        		<li>&nbsp;</li>
                        <li>&nbsp;</li>
                        <li class="dropdown">
    	          			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Hola, {{ Auth::user()->name.' '. Auth::user()->last_name }} <b class="caret"></b></a>
    	          			<ul class="dropdown-menu">
    	            			<li>
                                    <a href="{{URL::to('logout')}}">Cerrar sesión</a>
                                </li>
    	          			</ul>
    	        		</li>
          			@else
    	        		<li style="margin-right:50px;">
            				<a href="{{ URL::to('login') }}">Iniciar sesión</a>
    	        		</li>
          			@endif
        		</ul>
      		</div>
    	</nav>
    	<div id="wrapper">
            <!-- Sidebar -->
            <div id="sidebar-wrapper" style="height:100%;">
                <ul class="sidebar-nav">
                    <li class="sidebar-brand"></li>
                    <li>
                        {{-- <a href="http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/delivery_pidafacil"> --}}
                        <a href="{{URL::route('delivery_pidafacil')}}">
                        	<i class="fa fa-desktop fa-2x" style="float:left;"></i>
                        </a>
                    </li>
                    <li class="sidebar-brand"></li>
                    <li>
                        {{-- <a href="http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/mapaG"> --}}
                        <a href="{{URL::route('mapaG')}}">
                        	<i class="fa fa-motorcycle fa-2x" style="float:left;"></i>
                        </a>
                    </li>
                    <li class="sidebar-brand"></li>
                    <li>
                        {{-- <a href="http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/pagoM"> --}}
                        <a href="{{URL::route('pagoM')}}">
                            <i class="fa fa-users fa-2x" style="float:left;"></i>
                        </a>
                    </li>
                    <li class="sidebar-brand"></li>
                    <li>
                        {{-- <a href="http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/listMoto"> --}}
                        <a href="{{URL::route('listMoto')}}">
                            <i class="fa fa-pencil-square-o fa-2x" style="float:left;"></i>
                        </a>
                    </li>
                    <li class="sidebar-brand"></li>
                    <li>
                        <a href="{{URL::route('listRest')}}">
                            <i class="fa fa-shopping-cart fa-2x" style="float:left;"></i>
                        </a>
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
        <style type="text/css">
            #WindowLoad{
                position:fixed;
                top:0px;
                left:0px;
                z-index:3500;
                filter:alpha(opacity=65);
               -moz-opacity:65;
                opacity:0.65;
                background:#999;
            }
        </style>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <!-- cdn for modernizr, if you haven't included it already -->
        <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
        <!-- polyfiller file to detect and load polyfills -->
        <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
        {{ HTML::script('js/jquery.validate.min.js') }}
        {{HTML::script('js/ajax.js')}}
        <script>
            webshims.setOptions('waitReady', false);
            webshims.setOptions('forms-ext', {types: 'datetime-local'});
            webshims.polyfill('forms forms-ext');

            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
        </script>

        <script type="text/javascript">
            // alternative to load event
            document.onreadystatechange = function () {
                if (document.readyState != "complete") {
                    
                    console.log('aplicacion cargando');

                    //centrar imagen gif
                    height = 20;//El div del titulo, para que se vea mas arriba (H)
                    var ancho = 0;
                    var alto = 0;
                    var mensaje = "Cargando..";
                 
                    //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
                    if (window.innerWidth == undefined) ancho = window.screen.width;
                    else ancho = window.innerWidth;
                    if (window.innerHeight == undefined) alto = window.screen.height;
                    else alto = window.innerHeight;
                 
                    //operación necesaria para centrar el div que muestra el mensaje
                    var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar
                
                    //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
                    imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#000;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold'>" + mensaje + "</div>";

                    //ESTO <img src='http://images.pf.techmov.co/ajax-loader.gif'></div> VA LUEGO DEL CIERRE DEL DIV ARRIBA DE ESTE COMENTARIO
                    
                    //creamos el div que bloquea grande------------------------------------------
                    div = document.createElement("div");
                    div.id = "WindowLoad"
                    div.style.width = ancho + "px";
                    div.style.height = alto + "px";
                    $("body").append(div);
             
                    //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras 
                    input = document.createElement("input");
                    input.id = "focusInput";
                    input.type = "text"
                    
                    //asignamos el div que bloquea
                    //$("#WindowLoad").append(input);
             
                    //asignamos el foco y ocultamos el input text
                    $("#focusInput").focus();
                    $("#focusInput").hide();
             
                    //centramos el div del texto
                    $("#WindowLoad").html(imgCentro);                

                }else{
                    console.log('aplicacion completa');
                    $("#WindowLoad").remove();                    
                }
            }
        </script>
    	{{ HTML::script('js/boobstrap/bootstrap.js') }}
    	{{ HTML::script('http://api.html5media.info/1.1.5/html5media.min.js')}}
        {{ HTML::script('js/jquery.creditCardValidator.js', array("type" => "text/javascript")) }}        
    </body>
</html>
