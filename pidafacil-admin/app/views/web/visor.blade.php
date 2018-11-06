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
					@if(Auth::check())
						<li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li>
					@else
						<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
					@endif
					</ul>
				</li>
				<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>
			</li>
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
		<a href="#"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-shopping-cart fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['pending'] + $stats['registered'] }}</div>
							<div>Nuevas ordenes</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="#"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
			<div class="panel panel-accepted">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-plus fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['accepted'] }}</div>
							<div>Aceptadas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="#"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
			<div class="panel panel-completed">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-check-square fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['delivered'] }}</div>
							<div>Completadas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="#"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
			<div class="panel panel-canceled">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-minus-circle fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['cancelled'] }}</div>
							<div>Canceladas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="#"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
			<div class="panel panel-reject">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-thumbs-o-down fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['rejected'] }}</div>
							<div>Rechazadas</div>
						</div>
					</div>
				</div>
			</div>
		</div></a>
		<a href="#"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
			<div class="panel panel-uncollectable">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-times fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['uncollectible'] }}</div>
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
							</tr>
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
							
								@if($status->order_status_id == 1 || $status->order_status_id == 2)
								<tr>
									<td>{{ $order->order_id }}</td>
									<td>
										@if($order->service_type_id == 1)
											<i class="fa fa-motorcycle fa-2x fa-green"></i>
										@else
											<i class="fa fa-street-view fa-2x fa-yellow"></i>
										@endif
									</td>
									<td>
										@if($order->payment_method_id == 1)
											<i class="fa fa-money fa-2x" style="color:gray;"></i>
										@else
											<i class="fa fa-credit-card fa-2x" style="color:gray;"></i>
										@endif
									</td>
									<td>{{ $users->name . ' ' . $users->last_name }}</td>
									<td>{{ $order->address }}</td>
									<td>
										<button id="order_id_{{ $order->order_id }}" class="btn btn_edit button_100" data-toggle="modal" data-target="#order_detail_{{ $order->order_id.$order->service_type_id }}">Ver detalle</button>
									</td>
									<td>
										{{ Form::open(array('url'=>'/restaurant-orders/forward/'.$order->order_id, 'method'=>'POST')) }}
											{{ Form::submit('Aceptar', array('class'=>'btn btn_accepted button_100')) }}
										{{ Form::close() }}
									</td>
									<td>
										<button id="cancelled_by_{{ $order->order_id }}" class="btn btn_cancelled button_100" data-toggle="modal" data-target="#cancel_this_{{ $order->order_id }}">Rechazar</button>
									</td>
								</tr>
								<div class="modal fade" id="order_detail_{{ $order->order_id.$order->service_type_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div  class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="myModalLabel">Esta viendo el detalle de la orden {{ $order->order_id }}</h4>
											</div>
											<div class="modal-body">
											<!--
												<div class="row">	
													<div class="col-lg-2" style="color:#ef4036; font-size:18px;">Usuario:</div>
													<div class="col-lg-3" style="color:#ef4036; font-size:18px;">{{ $users->name.' '.$users->last_name }}</div>
													<div class="col-lg-2" style="color:#ef4036; font-size:18px;">Dirección:</div>
													<div class="col-lg-5" style="color:#ef4036; font-size:18px;">{{ $order->address }}</div>
												</div> -->
													<div class="row">
														<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
															<h1>{{ $users->name. ' ' .$users->last_name  }}</h1>
															<p>{{ $order->address }}</p>
														</div>
														
														<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
															<h1 style="width:100%;">$ {{ $order->order_total }}</h1>
														</div>
													</div>
													<div class="row">
														<div class="col-lg-12">
															<h3>Detalle de los productos</h3>
														</div>
													</div>

												
													<div class="row" style="border-top:solid 1px #cccccc;">
														<div class="col-lg-1 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
															Cantidad
														</div>
														<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
															Producto
														</div>
														<div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
															Condiciones
														</div>
														<div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
															Ingredientes
														</div>
														<div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
															Comentario
														</div>
														<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
															Precio Unidad
														</div>
														<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
															Precio Total
														</div>
													</div>
													@foreach($order->products as $ke=>$products)
															@foreach($products->conditions as $k=>$condition)
																<?php $conditions = $condition; ?>
															@endforeach
															@foreach($products->ingredients as $k=>$v)
																<?php $ingredients = $v; ?>
															@endforeach
													<div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
														<div class="col-lg-1 col-md-1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
															<br>
															{{ $products->quantity }}
														</div>
														<div class="col-lg-3 col-md-3" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
															<br>
															{{ $products->product }}
														</div>
														<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
															@if(isset($contions) && count($conditions) > 0)
																{{ $conditions->condition.': '.$conditions->condition_option }}
															@else
																No tiene condiciones
															@endif
														</div>
														<div class="col-lg-2 col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
															@if(isset($ingredients) && count($ingredients) > 0)
																{{ $ingredients->ingredient }}
															@else
																No tiene ingredientes
															@endif
														</div>
														<div class="col-lg-2 col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
															
														</div>
														<div class="col-lg-1 col-md-1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
															<br>
															$ {{ $products->unit_price }}
														</div>
														<div class="col-lg-1 col-md-1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
															<br>
															$ {{ $products->total_price }}
														</div>
													</div>
													@endforeach	
											</div>
											<div class="modal-footer">
										        <button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
											</div>
										</div>
									</div>
								</div>
								<div class="modal fade" id="cancel_this_{{ $order->order_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div  class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="myModalLabel">Cancelando orden N° {{ $order->order_id }}</h4>
											</div>
											<div class="modal-body">		
												{{ Form::open(array('url'=>'/restaurant-orders/cancel/'.$order->order_id, 'method'=>'POST')) }}
													<div class="container">
														<label style="color:red;" for="razon">Seleccione la acción a ejecutar</label>
														<div class="row">
															<div class="form-inline">
																<div class="btn btn-default button_150">
																{{ Form::radio('rejected', 6, true, array('id'=>'rejected_6_'.$order->order_id)) }} <label for="rejected_6_{{ $order->order_id }}">Cancelar</label>
																</div>
																<div class="btn btn-default button_150">
																{{ Form::radio('rejected', 7, false, array('id'=>'rejected_7_'.$order->order_id)) }} <label for="rejected_7_{{ $order->order_id }}">Rechazar</label>
																</div>
																<div class="btn btn-default button_150">
																{{ Form::radio('rejected', 8, false, array('id'=>'rejected_8_'.$order->order_id)) }} <label for="rejected_8_{{ $order->order_id }}">Incobrable</label>
																</div>
															</div>
														</div>	
													</div>
												<br>
												<div class="form-group">
													<label style="color:red;" for="razon">Escriba a continuación el motivo de la acción</label>
													{{ Form::textarea('comment', null, array('class'=>'form-control', 'required')) }}
												</div>
												<div class="form-group">
													{{ Form::submit('Ejecutar', array('class'=>'btn btn-default')) }}
												</div>
											{{ Form::close() }}
										</div>
									</div>
								</div>
								@else
								@endif
							@endforeach
						</table>
					</div>
				</div>
			<!-- /.panel-body -->
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
			<div class="panel panel-delivery">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-motorcycle fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['delivery'] }}</div>
							<div>A domicilio</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-pickup">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-street-view fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div class="huge">{{ $stats['pickup'] }}</div>
							<div>Para llevar</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	function startTime(){
		var today=new Date();
		var h=today.getHours();
		var m=today.getMinutes();
		var s=today.getSeconds();
		// add a zero in front of numbers<10
		h=checkTime(h);
		m=checkTime(m);
		s=checkTime(s);
		document.getElementById('txt').innerHTML=h+":"+m+":"+s;
		t=setTimeout(function(){startTime()},500);
	}

	function checkTime(i){
		if (i<10)
		  {
		  i="0" + i;
		  }
		return i;
	}
</script>	
@stop