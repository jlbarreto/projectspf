@extends('general.general_white')
@section('content')
<?php
	$cart = Session::get('cart');
	$cantidad = Session::get('cart2');	
?>

<input type="hidden" id="cont" value="{{$cantidad}}">
<nav class="navbar navbar-default" role="navigation">
  	<div class="navbar-header">
    	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
     		<i class="fa fa-bars fa-2x"></i>
    	</button>
		<a class="navbar-brand" href="{{ URL::to('') }}" onclick="cartA()">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
  	</div>
  	<div class="collapse navbar-collapse navbar-ex1-collapse">
    	<ul class="nav navbar-nav">
      		<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Inicio <b class="caret"></b></a>
				<ul class="dropdown-menu">
				<li><a href="{{ URL::to('explorar') }}" onclick="cartA()">Explorar</a></li>
				<li id="descApp1" style="display:none;"><a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil">Descargar la App</a></li>
                <li id="descApp2" style="display:none;"><a href="https://itunes.apple.com/us/app/id990772385">Descargar la App</a></li>
                @if($promociones >= 1)<li><a href="{{ URL::to('promociones') }}" onclick="cartA()">Promociones</a></li>@endif
				<li><a href="{{ URL::to('user/orders') }}" onclick="cartA()">Repetir pedido</a></li>
				<li class="divider"></li>
				<li><a href="{{ URL::to('cart') }}" onclick="addC()">Carrito de Compras</a></li>
				<li><a href="{{ URL::to('profile') }}" onclick="cartA()">Mi perfil</a></li>
                    <li class="divider"></li>
                    @include('../include/linckChat')
                    <li class="divider"></li>
				@if(Auth::check())
					<li><a href="{{ URL::to('logout') }}" onclick="cartA()">Cerrar sesión</a></li>
				@else
					<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
				@endif
				</ul>
			</li>
			<!--<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>-->
			<li><a href="{{ URL::to('cart') }}" onclick="procesar()"><span id="iconoContador">{{$cantidad}}&nbsp;</span><i class="fa fa-shopping-cart fa-lg"></i> Carrito de Compras</a></li>
    	</ul>
	    <form class="navbar-form navbar-left search-bar" role="search">
			<div class="form-group">
				<div class="input-group">
					<label for="tags" class="input-group-addon red-label">
						<i class="fa fa-search"></i>
					</label>
				  	<input type="text" id="tags" name="tags" class="form-control searchTags" placeholder="Buscar">
				</div>
			</div>
		</form>
	    <ul class="nav navbar-nav navbar-right">
			@if(Auth::check())
				@if(Auth::user()->name == '')
					<li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->email }} </a></li>
				@else
					<li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
	            @endif
	            <!-- <li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li> -->
	            @include('../include/linckChat')
			@else
				<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
				<li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
			@endif
	    </ul>
    </div>
</nav>
<div class="container white_content" style="padding-bottom: 1em;">
	<h1 class="text-center">Carrito de compras</h1>
	<div class="row">
		@if(isset($cart) && count($cart)>0)
			<?php $total_order=0; ?>
			@foreach($cart as $key=>$products)
				@if(is_array($products))
					<div class="col-md-9 text-left">
						<div class="table-responsive types">
							<table class="table">
						      	<caption><h3 style="color: #FFF;">Detalle de t&uacute; orden.</h3></caption>
						      	<thead>
						        	<tr>
						          	<th>Qty.</th>
						          	<th>Producto</th>
						          	<th>Condiciones</th>
						          	<th>Ingredientes</th>
						          	<th>Observaciones</th>
						          	<th>Precio</th>
						          	<th>Total</th>
						        	</tr>
						      	</thead>
						      	<tbody>
									@foreach($products as $ke=>$val)
										<?php $total_order += $val['total_price']; ?>
										<?php $price_id = 'price_'.$ke; ?>
										<tr>
								          	<th scope="row">
								          		{{ Form::open(array('url'=>'cart/update','method'=>'post', 'id' => "frmProduct$ke", 'class' => 'form-inline')) }}
								          		{{ Form::hidden('product_id', $val['product_id']) }}
												{{ Form::hidden('key', $ke) }}
												{{-- Form::input('number', 'quantity', $val['quantity'], array('class'=>'quantity-mod', 'style'=>'color: #666;')) --}}
												{{ Form::selectRange(
													'quantity', 1, 10, $val['quantity'],
													[
														'onchange'=>"$(this).changePrice('frmProduct".$ke."');",
														'class' => 'form-control input-sm quantity-mod'
													]) }}
												<!--<button
													onclick="$(this).changePrice('frmProduct{{-- $ke --}}');"
													class="btn btn-primary active btn-xs" id="btn{{-- $ke --}}">
													Actualizar
												</button>-->
								          		{{ Form::close() }}
								          	</th>
								          	<td>
								          		<strong>{{ $val['product'] }}</strong><br/>
								          		{{ $val['description'] }}
								          		{{ Form::open(array('url'=>'cart/delete','method'=>'post', 'class' => 'delete-form' ,'id' => 'frmProductDel'.$ke)) }}
												{{ Form::hidden('key', $ke) }}
												{{ Form::submit('Eliminar', array('class' => 'btn btn-default btn-xs')) }}
												{{ Form::close() }}
								          	</td>
								          	<td nowrap="nowrap">
								          		@if($val['conditions'] != null)
													@foreach($val['conditions'] as $condi)
														{{ $condi['condition_condition'] }}:<br/>
														<strong>{{ $condi['condition_option'] }}</strong><br/>
													@endforeach
												@else
													No aplica.
												@endif
								          	</td>
								          	<td nowrap="nowrap">
								          		@if($val['ingredients'] != null)
													@foreach($val['ingredients'] as $ingre)
														{{ ($ingre['active'] == 1 ? 'Con ' : 'Sin ') . $ingre['ingredient'] }}<br/>
													@endforeach
												@else
													No aplica.
												@endif
								          	</td>
								          	<td>{{ $val['comment'] }}</td>
								          	<td class="text-right">$&nbsp;{{ number_format($val['unit_price'],2) }}</td>
								          	<td class="text-right"><strong>$&nbsp;{{ number_format($val['total_price'],2) }}</strong></td>
								        </tr>
									@endforeach
							  	</tbody>
							</table>
						</div>
					</div>
				@else
					<div class="col-md-3 text-left" style="padding: 0px;">
						<div class="detail gray_content" style="margin-bottom: 1em;">
							<h2>{{ $products }} </h2>
							<h4>El sub total a pagar es: <strong>$ {{number_format( $total_order, 2) }}</strong></h4>
								<?php Session::put('total_order', $total_order); ?>
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
                                    <a href="{{ URL::to('cart/checkout') }}" class="btn btn-success" onclick="procesar()" style="background-color:#329E32; border-color:#329E32;">Proceder a la compra</a>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
									{{ Form::open(array('url' => 'cart/destroy', 'method' => 'post', 'id' => 'destroy'.$key)) }}
										{{ Form::submit('Cancelar', array('class'=>'btn btn-primary active', 'onclick' => 'cartA()')) }}
									{{ Form::close() }}
								</div>
							</div>
						</div>
						<a href="{{ URL::to($restaurant->slug) }}" class="btn btn-primary active btn-block" style="background-color:#032992; border-color:#032992; color:white;">Seguir comprando</a>
					</div>
				@endif
			@endforeach
		@else
			<span class="hide_small">
				<div class="col-lg-8 col-md-6 col-sm-6 col-xs-12" style="backgroun-color: #333333;">
					{{ HTML::image('images/step1.png', 'steps', array('class' => 'img_steps')) }}
					{{ HTML::image('images/step2.png', 'steps', array('class' => 'img_steps')) }}
					{{ HTML::image('images/step3.png', 'steps', array('class' => 'img_steps')) }}
					{{ HTML::image('images/cart_helper_1.png', 'helper', array('style' => 'padding: 15px; background-color: #3e3e3e; width: 100%;')) }}
					{{ HTML::image('images/cart_helper_2.png', 'helper', array('style' => 'padding: 15px; background-color: #3e3e3e; width: 100%;')) }}
				</div>
			</span>
			<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
				<h1>Tu carrito de compras esta actualmente vac&iacute;o</h1>
				<p>El carrito de compras se actualiza cuando ingresas a un restaurante y seleccionas algun producto y das click en "Agregar al carrito"</p>
				<p>Puedes buscar a los restaurantes en nuestra sección de
					<b><a href="{{ URL::to('explorar') }}">explorar</a></b>.
				</p>
			</div>
		@endif
	</div>
</div>

<script>
    function procesar(){
        appboy.logCustomEvent("Processing Order");

        //AppboyBinding.getUser().SetCustomUserAttribute("Tipo pago", "Efectivo");
        /*appboy.getUser().setCustomAttribute("Tipo pago", "Cash, Tigo Money");
        appboy.getUser().setCustomUserAttribute(
		  'Tipo pago',
		  'Efectivo'
		);*/
    }

    function addC(){
    	appboy.logCustomEvent("Ir al Carrito");
  	}

  	function cartA(){
  		appboy.logCustomEvent("Cart Abandoned");
  	}

	$(document).ready(function(){
		var dispositivo = navigator.userAgent.toLowerCase();
	  	if( dispositivo.search(/iphone|ipod|ipad|android/) > -1 ){
	  		//alert("Esta navegando en un movil");

	  		var isMobile = {
			    Android: function() {
			        return navigator.userAgent.match(/Android/i);
			    },
			    BlackBerry: function() {
			        return navigator.userAgent.match(/BlackBerry/i);
			    },
			    iOS: function() {
			        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
			    },
			    Opera: function() {
			        return navigator.userAgent.match(/Opera Mini/i);
			    },
			    Windows: function() {
			        return navigator.userAgent.match(/IEMobile/i);
			    },
			    any: function() {
			        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
			    }
			};

			if(isMobile.iOS()){
				$("#descApp2").show();
				$("#descApp1").hide();
			}else if(isMobile.Android()){
				$("#descApp1").show();
				$("#descApp2").hide();
			}
	  	}

	  	if($("#cont").val() > 0){
	  		$("#iconoContador").show();
	  	}else{
	  		$("#iconoContador").hide();
	  	}
	});	

</script>
@stop