@extends('general.master_page')
@section('fContent')
<?php $i = 0; ?>
@foreach($orders as $key=>$value)
	<?php 
		$finish_order = null;
		$pending_order = null;
	?>
	@if(empty($value)) <?php continue; ?>
	@else
		@if($i==0)
			<?php $last_order = $value; ?>
		@endif
		@foreach($value->status_logs as $k=>$val)
			@if($val->order_status_id > 4)
				<?php //$finished[] = $value;
					$finish_order = $value;
				?>
			@elseif($val->order_status_id < 5)
				<?php //$pending[] = $value; 
					$pending_order = $value;
				?>
			@endif
		@endforeach
		<?php 
			if (!empty($finish_order)) {
				$finished[] = $finish_order;
			}elseif(!empty($pending_order)){
				$pending[] = $pending_order;
			}
		?>
	<?php $i++; ?>
	@endif
@endforeach
<nav class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
            data-target=".navbar-ex1-collapse">
     	<i class="fa fa-bars fa-2x"></i>
    </button>
	<a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
  </div>
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Inicio <b class="caret"></b></a>
				<ul class="dropdown-menu">
				<li><a href="{{ URL::to('/') }}">Inicio</a></li>
				<li><a href="{{ URL::to('promociones') }}">Promociones</a></li>
				<li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
				<li><a href="{{ URL::to('explorar') }}">Explorar</a></li>
				<li class="divider"></li>
				<li><a href="{{ URL::to('cart') }}">Carrito</a></li>
				<li><a href="{{ URL::to('profile') }}">Mi perfil</a></li>
				<li class="divider"></li>
				@if(Auth::check())
					<li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li>
				@else
					<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
				@endif
				</ul>
			</li>
			<!--<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>-->
			<li><a href="{{ URL::to('cart') }}"><i class="fa fa-shopping-cart fa-lg"></i> Carrito</a></li>
    </ul>
    <form class="navbar-form navbar-left search-bar" role="search">
		<div class="form-group">
			<div class="input-group">
				<label for="tags" class="input-group-addon red-label">
					<i class="fa fa-search"></i>
				</label>
			  	<input type="text" id="tags" name="tags" class="form-control searchTags" placeholder="Search">
			</div>
		</div>
	</form>
    <ul class="nav navbar-nav navbar-right">
		@if(Auth::check())
			<li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
			<li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li>
		@else
			<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
			<li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
		@endif
    </ul>
  </div>
</nav>
<div class="container below_bar white_content">
	<div class="center_content">
		<span class="hide_small"><h1>Conoce el estado de tus &oacute;rdenes y repite pedidos.</h1></span>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space">
				<h3>Tu último pedido fue hecho en:</h3>
				@if(isset($last_order))
					<h4>{{ $last_order->restaurant->name }} 
						{{ Form::open(array('url' => 'user/repeat', 'method' => 'POST', 'class' => 'form-inline', 'style' => 'display:inline')) }}
							{{ Form::hidden('oid', $last_order->order_id) }}
    						<button
    							class="btn btn-default" 
		                        type="button" 
		                        data-toggle="modal" 
		                        data-target="#confirmRepeat" 
		                        data-title="Repetir Pedido" 
		                        data-message="&iquest;Est&aacute;s seguro que deseas repetir el pedido?, esto reemplazar&aacute; los productos que tengas en tu carrito de compras.">Repetir pedido</button>
						{{ Form::close() }}
					</h4>
					<table class="table table-bordered">
						<tr>
							<td>Cant.</td>
							<td>Producto</td>
							<td>Precio</td>
						</tr>
						@foreach($last_order->products as $id=>$products)
						<tr>
							<td>{{ $products->quantity }}</td>
							<td>{{ $products->product }}</td>
							<td>{{ '$ '. $products->total_price }}</td>
						</tr>
						@endforeach
					</table>
					<p>{{'Total $ ' . $last_order->order_total }} </p>
				@else
					<h3>No ha realizado un pedido</h3>
				@endif
			</div>
		</div>
		<div class="row">
			<div class="space">
				<a id="history" class="btn btn-primary button_150" href="#user-history">Historial</a>
				<a id="proccess" class="btn btn-primary button_150" href="#user-current">En proceso</a>
			</div>
			@if(!empty($pending))
				<div class="space">
					<p><i>La página se refrescar&aacute; cada cinco minutos para que conozcas el estado de tus &oacute;rdenes en proceso.</i></p>	
				</div>
			@endif
		</div>
	</div>
</div>
@stop
@section('content')
<div class="container gray_content">
	<div class="row" id="user-history"  style="display:none;">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			@if(!empty($finished))
				@foreach($finished as $key => $value)
					<?php $date = substr($value->created_at, 0, -9); ?>
					<div class="panel panel-default">
						<div class="panel-heading">{{ $value->restaurant->name.' '.$date}}
							<div class="pull-right">
								{{ Form::open(array('url' => 'user/repeat', 'method' => 'POST', 'class' => 'form-inline', 'style' => 'display:inline')) }}
								{{ Form::hidden('oid', $value->order_id) }}
    							<button
    							class="btn btn-default btn-xs" 
		                        type="button" 
		                        data-toggle="modal" 
		                        data-target="#confirmRepeat" 
		                        data-title="Repetir Pedido" 
		                        data-message="&iquest;Est&aacute;s seguro que deseas repetir el pedido?, esto reemplazar&aacute; los productos que tengas en tu carrito de compras.">Repetir pedido</button>
								{{ Form::close() }}
							</div>
							<div class="clear-fix"></div>
						</div>
						<table class="table table-bordered table-responsive">
							<tr>
								<td>Cant.</td>
								<td>Producto</td>
								<td>Precio</td>
								<td>Total</td>
							</tr>
							@foreach($value->products as $id=>$products)
								<tr>
									<td>{{ $products->quantity }}</td>
									<td>{{ $products->product }}</td>
									<td>{{ '$ ' . $products->unit_price }}</td>
									<td>{{ '$ ' . $products->total_price }}</td>
								</tr>
							@endforeach
						</table>
						@foreach($value->status_logs as $log=>$status)
								@if($status->order_status_id == 5)
									<div class="order_completed order_entregada">
										<p>Estado: entregada {{ $status->created_at }} </p>
									</div>
								@elseif($status->order_status_id == 6)
									<div class="order_completed order_cancelada">
										<p>Estado: cancelada {{ $status->created_at }} </p>
									</div>
								@elseif($status->order_status_id == 7)
									<div class="order_completed order_rechazad">
										<p>Estado: rechazada {{ $status->created_at }} </p>
									</div>
								@elseif($status->order_status_id == 8)
									<div class="order_completed order_incobrable">
										<p>Estado: incobrable {{ $status->created_at }} </p>
									</div>
								@endif
							@endforeach
					</div>
				@endforeach
			@else
				<h2>No hay &oacute;rdenes completas por el momento</h2>
				<p>Ninguna orden ha recibido un estado de finalización (Entregada, Cancelada, Rechazada o Incobrable).</p>
				<p>Te recomendamos estar pendiente a los cambios que el restaurante pueda asignarle a tu orden.</p>
			@endif		
		</div>	
	</div>
	<div class="row" id="user-current" style="display:none;">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			@if(!empty($pending))
				@foreach($pending as $key=>$value)
					<?php $date = substr($value->created_at, 0, -9); ?>
					<div class="panel panel-default">
						<div class="panel-heading">{{ $value->restaurant->name.' '.$date }}
							<div class="pull-right">
								{{ Form::open(array('url' => 'user/repeat', 'method' => 'POST', 'class' => 'form-inline', 'style' => 'display:inline')) }}
								{{ Form::hidden('oid', $value->order_id) }}
    							<button
    							class="btn btn-default btn-xs" 
		                        type="button" 
		                        data-toggle="modal" 
		                        data-target="#confirmRepeat" 
		                        data-title="Repetir Pedido" 
		                        data-message="&iquest;Est&aacute;s seguro que deseas repetir el pedido?, esto reemplazar&aacute; los productos que tengas en tu carrito de compras.">Repetir pedido</button>
								{{ Form::close() }}
							</div>
							<div class="clear-fix"></div>
						</div>
							<table class="table table-bordered table-responsive">
								<tr>
									<th>Cant</th>
									<th>Producto</th>
									<th>Precio</th>
									<th>Valor total</th>
								</tr>
								@foreach($value->products as $id=>$products)
								<tr>
									<td>{{ $products->quantity }}</td>
									<td>{{ $products->product }}</td>
									<td>{{ '$ ' . $products->unit_price }}</td>
									<td>{{ '$ ' . $products->total_price }}</td>
								</tr>
								@endforeach
							</table>
							@foreach($value->status_logs as $log=>$status)
								@if($status->order_status_id == 1)
									<div class="order_completed order_pendiente">
										<p>Estado: pendiente {{ $status->created_at }} </p>
									</div>
								@elseif($status->order_status_id == 2)
									<div class="order_completed order_registrada">
										<p>Estado: registrada {{ $status->created_at }} </p>
									</div>
								@elseif($status->order_status_id == 3)
									<div class="order_completed order_aceptada">
										<p>Estado: aceptada {{ $status->created_at }} </p>
									</div>
								@elseif($status->order_status_id == 4)
									<div class="order_completed order_despachada">
										<p>Estado: despachada {{ $status->created_at }} </p>
									</div>
								@endif
							@endforeach
							<div class="panel-footer">{{ 'Total: $ '. $value->order_total }} </div>
						</div>	
				@endforeach
			<script type="text/javascript">
				$.fn.countdown = function (callback, duration, message) {
					message = message || "";
					var container = $(this[0]).html(duration + message);
					var countdown = setInterval(function () {
						if (--duration) {
							container.html(duration + message);
						} else {
							clearInterval(countdown);
							callback.call(container);   
						}
						}, 1000);
					};
					$(".countdown").countdown(redirect, 300, "s remaining");
					function redirect () {
					this.html("Done counting, redirecting.");
					location.reload();
				}															
			</script>
			@else
				<p>No tienes ninguna orden en proceso; te recomendamos que si tienes productos en el 
				<a href="{{ URL::to('cart') }}">carrito</a> completes la orden.</p>
			@endif
		</div>	
	</div>
</div>

<!-- Modal Dialog -->
<div class="modal fade" id="confirmRepeat" role="dialog" aria-labelledby="confirmRepeatLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Repetir Pedido</h4>
      </div>
      <div class="modal-body">
        <p>&iquest;Est&aacute;s seguro que deseas repetir el pedido?, esto reemplazar&aacute; los productos que tengas en tu carrito de compras.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary active" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-default" id="confirm">Repetir</button>
      </div>
    </div>
  </div>
</div>
<!-- Dialog show event handler -->
<script type="text/javascript">
$(document).ready(function() {
  	$('#confirmRepeat').on('show.bs.modal', function (e) {
      $message = $(e.relatedTarget).attr('data-message');
      $(this).find('.modal-body p').text($message);
      $title = $(e.relatedTarget).attr('data-title');
      $(this).find('.modal-title').text($title);

      // Pass form reference to modal for submission on yes/ok
      var form = $(e.relatedTarget).closest('form');
      $(this).find('.modal-footer #confirm').data('form', form);
  	});

  	<!-- Form confirm (yes/ok) handler, submits form -->
  	$('#confirmRepeat').find('.modal-footer #confirm').on('click', function(){
      $(this).data('form').submit();
  	});
});
</script>

<script type="text/javascript">
	$('#history').click(function(){
		$(this).addClass('active');
		$('#proccess').removeClass('active');
		$('#user-current').hide();
		$('#user-history').show();
	});

	$('#proccess').click(function(){
		$(this).addClass('active');
		$('#history').removeClass('active');
		$('#user-history').hide();
		$('#user-current').show();
	});
</script>
@stop