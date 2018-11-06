@extends('general.visor_template')
@section('content')
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<i class="fa fa-bars fa-2x"></i>
		</button>
		<a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
	</div>
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
			<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Menú <b class="caret"></b></a>
					<ul class="dropdown-menu">
					<li><a href="{{ URL::to('promociones') }}">Sucursales</a></li>
					<li><a href="{{ URL::to('#') }}">Menú de restaurante</a></li>
					<li><a href="{{ URL::to('#') }}">Acerca de nosotros</a></li>
					<li><a href="{{ URL::to('#') }}">Call Center</a></li>
					<li><a href="{{ URL::to('#') }}">Historico de ordenes</a></li>
                        <li class="divider"></li>
                        @include('../include/linckChat')
                        <li class="divider"></li>
					@if(Auth::check())
						<li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li>
					@else
						<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
					@endif
					</ul>
				</li>
				<li ><a href="#" style="display: inline-block;" id="btnBuscar"><i class="fa fa-search fa-lg"></i></a>{{Form::Text('buscar',null,array('id'=>'buscar','autocomplete'=>'off','class'=>'inputBuscar','placeholder'=>'Buscar'))}}</li>
			</li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			@if(Auth::check())
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Hola, {{ Auth::user()->name.' '. Auth::user()->last_name }} <b class="caret"></b></a>

					<ul class="dropdown-menu">
                        <!-- <li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li> -->
                        @include('../include/linckChat')
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
		<a href="?fillter=1" ><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
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
		<a href="?fillter=3"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
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
		<a href="?fillter=5" ><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
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
		<a href="?fillter=6"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
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
		<a href="?fillter=7"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
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
		<a href="?fillter=8"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
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
								<th>Aceptar</th>
								<th>Rechazar</th>
                                @if(isset($sucursales[0]))<th>Asignar a sucursal</th>@endif
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
                                            @if($status->order_status_id == $idF)
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
        if(!isset($sucursales[0]))
        {
    ?>
            var id_restaurante = 0;
    <?php
        }else{
    ?>
        var id_restaurante = {{$sucursales[0]->restaurant_id}};
    <?php
    }
    ?>
</script>
{{ HTML::script('js/functions/visor.js') }}
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
</script>
@stop
