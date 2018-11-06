@extends('general.visor_template')
@section('content')
    {{ HTML::script('js/functions/visor.js') }}
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<i class="fa fa-bars fa-2x"></i>
		</button>
		<a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
	</div>
	<div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
        @if(Auth::user()->role_id == 1)
			<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> {{ $restaurantes[$id]['name'] }} <b class="caret"></b></a>
					<ul class="dropdown-menu" style="overflow: scroll; height: 250px;">
                            @foreach($restaurantes as $key => $restaurant)
                                <li>
                                    <a href="{{ $key }}">{{ $restaurant['name'] }}</a>
                                </li>
                            @endforeach
					</ul>
				</li>
				<li ><a href="#" style="display: inline-block;" id="btnBuscar"><i class="fa fa-search fa-lg"></i></a>{{Form::Text('buscar',null,array('id'=>'buscar','autocomplete'=>'off','class'=>'inputBuscar','placeholder'=>'Buscar'))}}</li>
			</li>

            @else

            <li ><a href="#" style="display: inline-block;" id="btnBuscar"><i class="fa fa-search fa-lg"></i></a>{{Form::Text('buscar',null,array('id'=>'buscar','autocomplete'=>'off','class'=>'inputBuscar','placeholder'=>'Search'))}}</li>
            </li>
        @endif
        </ul>
		<ul class="nav navbar-nav navbar-right">
			@if(Auth::check())
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Hola, {{ Auth::user()->name.' '. Auth::user()->last_name }} <b class="caret"></b></a>

					<ul class="dropdown-menu">
						<li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li>
					</ul>
				</li>
			@else
				<li style="margin-right:50px;"><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
			@endif
		</ul>
	</div>
</nav>
<!-- PHP -->
<?php 
	$date = date("Y-m-d H:i:s");
?>
<!-- Fin de PHP e inicio contenido -->
<div class="container-fluid gray_content">
	<div class="row">
		<a href="?fillter=1" ><div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-shopping-cart fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge" id="pending">{{ $stats['pending']['fillter'] }}</div>
							<div>Nuevas ordenes</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="?fillter=3"><div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<div class="panel panel-accepted">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-plus fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge" id="accepted">{{ $stats['accepted']['fillter'] }}</div>
							<div>Aceptadas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>

        <a href="?fillter=4"><div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                <div class="panel panel-process">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-flag-checkered fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge" id="accepted">{{ $stats['dispatched']['fillter'] }}</div>
                                <div>Despachadas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div></a>

		<a href="?fillter=5" ><div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<div class="panel panel-completed">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-check-square fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge" id="delivered">{{ $stats['delivered']['fillter'] }}</div>
							<div>Completadas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="?fillter=6"><div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<div class="panel panel-canceled">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-minus-circle fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge" id="cancelled">{{ $stats['cancelled']['fillter'] }}</div>
							<div>Canceladas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="?fillter=7"><div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<div class="panel panel-reject">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-thumbs-o-down fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge" id="rejected">{{ $stats['rejected']['fillter'] }}</div>
							<div>Rechazadas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="?fillter=8"><div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
			<div class="panel panel-uncollectable">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-times fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge" id="uncollectible">{{ $stats['uncollectible']['fillter'] }}</div>
							<div>Incobrables</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
	</div>
	<div class="row">
		<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-shopping-cart fa-fw" style="color:#333333;"></i> Ordenes
				</div>
				<!-- /.panel-heading -->
				<div class="panel-body">
					<div id="morris-area-chart">
						<table class="table table-bordered">
							<tr>
								<th># Orden</th>
								<th>Servicio</th>
								<th>Pago</th>
								<th>Nombre</th>
								<th>Direccion</th>
                                <th>Detalle</th>
                                @if(!isset($_GET['fillter']) || (isset($_GET['fillter']) and $_GET['fillter'] == 1))
                                    @if(((Auth::user()->role_id != 1 && Auth::user()->role_id != 3) || $restaurantes[$id]['parent'] != $id) || !isset($sucursales[1]))
                                        <th>Aceptar</th>
								        <th>Rechazar</th>
                                    @endif
                                    @if(isset($sucursales[1]))
                                        @if(Auth::user()->role_id != 2)
                                            <th>Asignar a sucursal</th>
                                        @endif
                                    @endif
                                @else
                                        @if(!isset($_GET['fillter']) || $_GET['fillter'] < 5)
                                            <th></th>
                                        @endif
                                @endif
							</tr>
                            <?php

                                /*  Filtros

                                    se asigna un valor deacuerdo al id de la variable GET.
                                */
                                $filtro='';
                                if(isset($_GET['fillter']))
                                {
                                    $idF=$_GET['fillter'];
                                    if($idF==1)
                                    {
                                        $filtro='pending';
                                    }elseif($idF==3)
                                    {
                                        $filtro='accepted';
                                    }
                                    elseif($idF==4)
                                    {
                                        $filtro='dispatched';
                                    }elseif($idF==5)
                                    {
                                        $filtro='delivered';
                                    }elseif($idF == 6)
                                    {
                                        $filtro='cancelled';
                                    }
                                    elseif($idF == 7)
                                    {
                                        $filtro='rejected';
                                    }
                                    elseif($idF == 8)
                                    {
                                        $filtro='uncollectible';
                                    }
                                }else{
                                    $filtro='pending';
                                    $idF=1;
                                }
                                /*
                                 * Paginacion
                                 *
                                 * Cantidad de elementos paginados*/
                                $cantPaginados = 10;
                                /*Varibales, contador, hasta que numero se muestra, desde que numero se muestra...*/
                                $co = 0; $hasta = 0; $desde = 0;

                                //Se determina el ultimo elemento a mostrar
                                if(isset($_GET['page']))
                                {
                                    $pag=$_GET['page']; $pag = $pag*$cantPaginados;

                                }else{
                                    $pag=$cantPaginados;
                                }
                                /*Se determina el primer elemento a mostrar*/
                                $desde = $pag - $cantPaginados;

                                $hasta = $pag;
                            ?>
                            <!-- Se verifica si el restaurante o la sucursal tiene pedidos, si no los tiene envia mensaje-->
							@if(isset($orders))
                                @foreach($orders as $key=>$order)
                                    @foreach($order->users as $ke=>$val)
                                        <?php
                                            $users = $val;
                                        ?>
                                    @endforeach
                                    @foreach($order->status_logs as $ke=>$val)
                                        <?php
                                            $status = $val;
                                        ?>
                                    @endforeach
                                    @if((isset($_GET['busqueda']) && (isset($_GET['fillter']) || isset($_GET['forma']))) || (empty($_GET['busqueda']) && empty($_GET['fillter']) && empty($_GET['forma'])) || (isset($_GET['fillter']) || isset($_GET['forma'])))
                                            @if($idF == 1 && (($status->order_status_id == $idF && $order->service_type_id == 1) || ($status->order_status_id == $idF && $order->service_type_id == 2) || ($status->order_status_id == 2 && $order->service_type_id == 3) || ($status->order_status_id == 1 && $MasDeUnDespacho  && $order->service_type_id == 3)))
                                                @if(isset($_GET['forma']) && $order->service_type_id == $_GET['forma'])
                                                    @if($co < $hasta and $co >= $desde)
                                                        @include('include.elements_restaurant_orders')
                                                    @endif
                                                    <?php $co=$co + 1; ?>
                                                @elseif(!isset($_GET['forma']))
                                                    @if($co < $hasta and $co >= $desde)
                                                        @include('include.elements_restaurant_orders')
                                                    @endif
                                                    <?php $co=$co + 1; ?>
                                                @endif
                                            @elseif($idF != 1 and $status->order_status_id == $idF)
                                                @if(isset($_GET['forma']) && $order->service_type_id == $_GET['forma'])
                                                    @if($co < $hasta and $co >= $desde)
                                                        @include('include.elements_restaurant_orders')
                                                    @endif
                                                    <?php $co=$co + 1; ?>
                                                @elseif(!isset($_GET['forma']))
                                                    @if($co < $hasta and $co >= $desde)
                                                        @include('include.elements_restaurant_orders')
                                                    @endif
                                                    <?php $co=$co + 1; ?>
                                                @endif
                                            @endif
                                    @else
                                            @if($co < $hasta and $co >= $desde)
                                                Hola9
                                               @include('include.elements_restaurant_orders')
                                            @endif
                                            <?php $co=$co + 1; ?>
                                    @endif
                                @endforeach
                            @endif

						</table>
                        <strong><strong><strong>
                              <ul class="pagination">
                                  <li>
                                      @if(isset($_GET['fillter']))
                                        @if(isset($_GET['forma']))
                                            <a href="?fillter={{$_GET['fillter']}}&forma={{$_GET['forma']}}&page=1" rel="prev">«</a>
                                        @else
                                            <a href="?fillter={{$_GET['fillter']}}&page=1" rel="prev">«</a>
                                        @endif
                                      @else
                                          <a href="?page=1" rel="prev">«</a>
                                      @endif
                                  </li>
                                  <?php //Paginacion, numeros
                                        if(isset($_GET['forma']))
                                        {
                                            if($_GET['forma'] == 1)
                                            {
                                                $pag = $stats[$filtro]['delivery']/$cantPaginados;
                                            }else{
                                                $pag = $stats[$filtro]['pickup']/$cantPaginados;
                                            }
                                        }else{
                                            $pag = $stats[$filtro]['fillter']/$cantPaginados;
                                        }
                                  ?>
                                  @for($i = 0; $i < $pag; $i++)
                                      <li>
                                          @if(isset($_GET['fillter']))
                                              @if(isset($_GET['forma']))
                                                    <a href="?fillter={{$_GET['fillter']}}&forma={{$_GET['forma']}}&page={{$i+1}}">{{$i+1}}</a>
                                              @else
                                                  <a href="?fillter={{$_GET['fillter']}}&page={{$i+1}}">{{$i+1}}</a>
                                              @endif
                                          @else
                                              <a href="?page={{$i+1}}">{{$i+1}}</a>
                                          @endif
                                      </li>
                                  @endfor
                                  <li>
                                      @if(isset($_GET['fillter']))
                                          @if(isset($_GET['forma']))
                                              <a href="?fillter={{$_GET['fillter']}}&forma={{$_GET['forma']}}&page={{ceil($pag)}}" rel="next">»</a>
                                         @else
                                              <a href="?fillter={{$_GET['fillter']}}&page={{ceil($pag)}}" rel="next">»</a>
                                         @endif
                                      @else
                                          <a href="?page={{ceil($pag)}}" rel="next">»</a>
                                      @endif
                                  </li>
                              </ul>
                        </strong></strong></strong>
					</div>
				</div>
			<!-- /.panel-body -->
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <a href="@if(isset($_GET['fillter'])) ?fillter={{$_GET['fillter']}}&forma=1 @else ?fillter=1&forma=1 @endif">
                <div class="panel panel-delivery">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-motorcycle fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge" id="delivery">@if(isset($_GET['fillter'])){{ $stats[$filtro]['delivery'] }}@else {{$stats['pending']['delivery']}} @endif</div>
                                <div>A domicilio</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <a href="@if(isset($_GET['fillter'])) ?fillter={{$_GET['fillter']}}&forma=2 @else ?fillter=1&forma=2 @endif">
                <div class="panel panel-pickup">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-street-view fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge" id="pickup">@if(isset($_GET['fillter'])){{ $stats[$filtro]['pickup'] }}@else {{$stats['pending']['delivery']}} @endif</div>
                                <div>Para llevar</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
		</div>
	</div>
</div>
<script>
    /*Parametro necesario para las funciones internas*/
    <?php
        if(!isset($id))
        {
    ?>
            var id_restaurante = 0;
    <?php
        }else{
    ?>
        var id_restaurante = {{ $id }};
    <?php
    }
    ?>
</script>

<script>
    function busqueda()
    {
        @if(isset($_GET['fillter']))
            @if(isset($_GET['forma']))
                location.href = "?fillter={{$_GET['fillter']}}&forma={{$_GET['forma']}}&busqueda="+$("#buscar").val();
            @else
                location.href = "?fillter={{$_GET['fillter']}}&busqueda="+$("#buscar").val();
            @endif
        @elseif(isset($_GET['forma']))
            location.href = "?fillter={{$_GET['fillter']}}&forma={{$_GET['forma']}}&busqueda="+$("#buscar").val();
        @else
            location.href = "?busqueda="+$("#buscar").val();
        @endif
    }
/*
*
* Notificaciones en el escritorio.....
*
* */


//solicita permiso al usuario para lanzar las notificaciones
     function AskForWebNotificationPermissions()
    {
        if (Notification) {
            Notification.requestPermission();
        }
    }



//Asigna sonido
    function audioNotification(){
        var soundAlert = new Audio('../sound/alerta.mp3');
        soundAlert.play();
    }
    $( document ).ready(function() {
        AskForWebNotificationPermissions();
    });
</script>
{{ HTML::script('http://cdn.socket.io/socket.io-1.2.0.js') }}
{{ HTML::script('nodejs/configIp.js')  }}
<script>
    var idRes = '{{$_SERVER['REQUEST_URI']}}';
    idRes = idRes.split("/");
    idRes = idRes[idRes.length-1];
    if(idRes.split('?'))
    {
        idRes = idRes.split('?');
    }

    var socket = io.connect(ip);
    setInterval(function() {
        socket.emit('delivery_restaurant', idRes);
    },15000);
    socket.on('delivery_restaurant', function(msg){
        //Parametro necesario para lanzar notificaciones
        var options = {
            body: "Código de la orden: "+msg,
            icon: "data:image/png;base64,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"
        };
        //Ejecuta la notificacion
        var notif = new Notification("Nueva orden", options);
        notif.onshow = function(){
            audioNotification();
        }
        notif.onclick = function(){
            location.href="{{ $_SERVER['REQUEST_URI'] }}";
        }
    });
</script>
@stop