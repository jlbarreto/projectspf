@extends('general.restaurant_info_')
@section('restaurant')

<?php
  $cart = Session::get('cart');
  $cantidad = Session::get('cart2');
  $id = Session::get('idR');
?>

<input type="hidden" id="cont" value="{{$cantidad}}">
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
					<li><a href="{{ URL::to('explorar') }}">Explorar</a></li>
					<li id="descApp1" style="display:none;"><a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil">Descargar la App</a></li>
	                <li id="descApp2" style="display:none;"><a href="https://itunes.apple.com/us/app/id990772385">Descargar la App</a></li>
	                <!-- <li><a href="{{ URL::to('promociones') }}">Promociones</a></li> -->
					<li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
					<li class="divider"></li>
					<li><a href="{{ URL::to('cart') }}" onclick="addC()">Carrito de Compras</a></li>
					<li><a href="{{ URL::to('profile') }}">Mi perfil</a></li>
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
			<!--<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>-->
			<li><a href="{{ URL::to('cart') }}" onclick="addC()"><span id="iconoContador">{{$cantidad}}&nbsp;</span><i class="fa fa-shopping-cart fa-lg"></i> Carrito de Compras</a></li>
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
<div class="container">	

	<div class="col-lg-9 col-xs-12">
		<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 to-rest" align="center">
				<a href="{{ URL::to($restaurant->slug) }}">{{ HTML::imagepf($landingpage->logo,'',array('style'=>'height: 100px;')) }}<br/><strong>Ir a Restaurante</strong></a>
			</div>
			<div class="col-lg-8 col-md-4 col-sm-8 col-xs-9 white_content" align="center">
				<span class="hide_small"><h1 style="font-size: 50px;">{{ $restaurant->name }}</h1></span>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-xs-12">
		<div class="col-lg-12 rest-menu">
			<div class="row">
				<div class="col-xs-offset-3 col-xs-9">
					<div class="dropdown">
						<button class="btn btn-lg btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
					    	Ver Men&uacute;
					    	<span class="caret"></span>
					  	</button>
					  	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
					  		@foreach($sections as $k => $val)
					  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::to($restaurant->slug .'/sections/'.$val->section_id) }}">{{ $val->section }}</a></li>
					  		@endforeach
					  		<!--<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::to($restaurant->slug .'/promociones') }}">Promociones</a></li>-->
					  		@if($promociones >= 1)<li role="preseentation"><a role="menuitem" tabindex="-1" href="{{ URL::to($restaurant->slug .'/promociones') }}">Promociones</a></li>@endif
					  	</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
@section('content')
<div class="container white_content">
	<div class="row">
		<h1 class="text-left sec-title"><a href="{{ URL::to($restaurant->slug.'/sections/'.$product->section_id) }}">&lt; {{ $product->product }}</a></h1>
	</div>
	<div class="row">
		<div class="col-sm-6 col-xs-12">
			<div class="types" style="padding: 0px;">
				<div class="row">
					<div class="col-sm-6 col-xs-6">
						<div class="singleproduct_image">
							@if($product->image_web != null)
								<img src="{{ URL::to(Config::get('app.url_images').$product->image_web) }}" alt="Imagen de producto">
							@else
								<?php 
								$rest = Restaurant::where('restaurant_id', $product->restaurant_id)->with('landingpage')->first(); ?>
								{{ HTML::imagepf($rest->landingpage->logo, 'No hay imagen') }}
							@endif
						</div>
					</div>
					<div class="col-sm-6 col-xs-6">
						<div style="padding-right: 15px;">
							<h2> $ {{ $product->value }} </h2>
							<p> {{ $product->description }} </p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xs-12">
			<div class="types">
				{{ Form::open(array('url'=>'cart/add','method'=>'post', 'onsubmit'=>'return addToCart();', 'id'=>'frmAddPrd', 'class' => 'create_order', 'role' => 'form', 'data-toggle' => 'validator')) }}
					{{ Form::hidden('product_id', $product->product_id, array('id' => 'product_id')) }}
					{{ Form::hidden('r_id', $product->restaurant_id, array('id' => 'r_id')) }}
					{{ Form::hidden('r_ses', $id, array('id' => 'r_ses')) }}
					<div class="row">
						<div class="col-md-6 col-xs-12">
							{{ Form::label('quantity','Cantidad: ') }}
							{{ Form::selectRange('quantity', 1, 10, $val['quantity'], 
								[
									'class' => 'form-control',
									'style' => 'color: #FFF;'
								]) }}
							{{-- Form::quantity() --}}
						</div>
						<div class="col-md-6 col-xs-12 text-right">
		                    <button type="submit" id="ajax" class="btn btn-success btn-lg" style="background-color:#329E32; border-color:#329E32;" >Agregar al Carrito</button>
							<div class="modal text-left" id="add" tabindex="-1" role="dialog" aria-labelledby="">
								<div class="modal-dialog">
									<div class="modal-content">
										<!--<div  class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						    				<h3 class="modal-title" id="myModalLabel">¡Gracias por su compra!</h3>
										</div>-->
										<div class="modal-body">
											<p id="validate"></p>
										</div>
										<div class="modal-footer">
											<div id="botMov">
												<a href="{{ URL::to($restaurant->slug) }}" id="btnMov1" class="btn btn-default" style="background-color:#032992; border-color:#032992;">Seguir comprando</a>
												<a href="{{ URL::to('cart/') }}" id="btnMov2" class="btn btn-success" onclick="procesar()" style="background-color:#329E32; border-color:#329E32;">Ir al Carrito de Compras</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-xs-12">
							<h2>Condiciones</h2>
							@if(count($condition)> 0)
								@foreach($condition as $key => $value)
									<?php $options = array(); ?>
									<article class="condition form-group">
									@foreach($value->opciones as $k => $val)
										<?php $options[$val->condition_option_id] = $val->condition_option; ?>
									@endforeach
										{{ Form::label('condition'.$value->condition_id, $value->condition.':'); }}
										<br/>
										{{ Form::select('condition['.$value->condition_id.']', 
											[null=>'Seleccionar...'] + $options, 
											null,
											array(
												'id'=>'condition'.$value->condition_id, 
												'class'=>'form-control', 
												'title' => 'Por favor selecione una opci&oacute;n!',
												'required')) }}
									</article>
								@endforeach
							@else
								<p>Este producto no tiene condiciones</p>
							@endif
						</div>
						<div class="col-md-6 col-xs-12">
							<h2>Ingredientes</h2>
							<div class="ingredients_select">
								@if($ingredient->count() > 0)
									<ul class="white_content">
										<?php $ig = 0; ?>
										@foreach($ingredient as $key => $value)
											@if($value->pivot->removable == 1)
											<li>
												{{ Form::hidden('ingredients['.$value->ingredient_id.']', 0) }}
												{{ Form::checkbox('ingredients['.$value->ingredient_id.']', 1, true, array('id'=>'ingredient_'.$value->ingredient_id)) }}
												{{ Form::label('ingredient_'.$value->ingredient_id, $value->ingredient) }}
											</li>
												<?php $ig++ ?>
											@endif
										@endforeach						
										@if($ig == 0)
											<li>Este producto no tiene ingredientes removibles</li>
										@endif
									</ul>
								@else
									<ul>
										<li class="white_content">Este producto no tiene ingredientes removibles</li>
									</ul>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<h2>Comentario</h2>
							<p>Escriba un pequeño comentario al pedido (ej. <i>"Deseo el queso derretido"</i>).</p>
							{{ Form::textarea('comment', null, ['class' => 'form-control', 'rows'=>'3']) }}
						</div>
					</div>
					<div class="modal fade" id="modalNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
						    <div class="modal-content">
						      	<!--<div class="modal-header">
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							    	<h4 class="modal-title" id="myModalLabel" style="color:#444444;">Aviso</h4>
							    </div>-->
							    <div class="modal-body" style="color:#444444; text-align:center;">
							        ¿Desea agregar un producto de otro restaurante? 
							        <br>Si continua se perderá la orden anterior.
							    </div>
						      	<div class="modal-footer">
						        	<button type="button" id="noProduct" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						        	<button type="submit" id="newProduct" class="btn btn-default" style="background-color:#0B0BBD; border-color:#0B0BBD; color:white;">Continuar</button>
						      	</div>
						    </div>
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

<script>

	$('#modalNew').on('show.bs.modal', function () {
       $(this).find('.modal-dialog').css({
              width:'400px', //probably not needed
              height:'auto', //probably not needed 
              'max-height':'100%'
       });
	});

	/*function myFunction(event) {
	    var x;
	    var rest_s = $("#r_ses").val();
	    var rest_a = $("#r_id").val();
	    var contador = $("#cont").val();

	    if(contador > 0){
	    	if(rest_a != rest_s){
	    		event.preventDefault();
				$('#modalNew').modal('show');
			}
		}
	}*/

	$("#ajax").click(function(event){
	  	var x;
	    var rest_s = $("#r_ses").val();
	    var rest_a = $("#r_id").val();
	    var contador = $("#cont").val();

	    if(contador > 0){
	    	if(rest_a != rest_s){
	    		event.preventDefault();
				$('#modalNew').modal('show');
			}
		}

		//appboy.getUser().setCustomAttribute("Tipo pago", null);

		// Adding a new element to a custom attribute with an array value
		//appboy.getUser().addToCustomAttributeArray("Tipo pago", "Efectivo");
	});

	$("#newProduct").click(function(){
		$('#modalNew').modal('hide');
	});

    function addToCart(){
        appboy.logCustomEvent("Add To Cart");
        
        return true;
    }

    function procesar(){
        appboy.logCustomEvent("Processing Order");
    }

    function addC(){
		appboy.logCustomEvent("Ir al Carrito");
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
