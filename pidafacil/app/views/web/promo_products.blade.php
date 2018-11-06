@extends('general.restaurant_info_')
@section('restaurant')

<?php
  $cart = Session::get('cart');
  $cantidad = Session::get('cart2');
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
                @if($promociones >= 1)<li><a href="{{ URL::to('promociones') }}">Promociones</a></li>@endif
				<li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
				<li class="divider"></li>
				<li><a href="{{ URL::to('cart') }}" onclick="procesar()">Carrito de Compras</a></li>
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
<div class="container">
	<div class="col-lg-9 col-xs-12">
		<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 to-rest" align="center">
				<a href="{{ URL::to($restaurant->slug) }}">{{ HTML::imagepf($landingpage->logo,'',array('style'=>'height: 100px;')) }}<br/><strong>Ir a Restaurante</strong></a>
			</div>
			<div class="col-lg-8 col-md-4 col-sm-8 col-xs-9 white_content" align="center">
				<span class="hide_small">
					<h1>{{ $restaurant->name }}</h1>
				</span>
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
				  @foreach($restaurant->sections as $k => $val)
				  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::to($restaurant->slug .'/sections/'.$val->section_id) }}">{{ $val->section }}</a></li>
				  @endforeach
				  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::to($restaurant->slug .'/promociones') }}">Promociones</a></li>
				  </ul>
				</div>
				</div>
			<div>
		</div>
	</div>
</div>
@stop
@section('content')
<div class="container gray_content">
	<div class="row">
		{{-- BaseController::bread() --}}
		<h1 class="text-left sec-title"><a href="{{ URL::to('promociones') }}">&lt; Promociones</a></h1>
	</div>
	<div class="row">
		<div class="col-lg-12 center_content">
			<div class="row">
			@foreach($products as $key => $product)
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 space_15">
					<!--<a href="{{-- URL::to($restaurant->slug.'/'.$product->slug) --}}">-->
						<section class="product">
							<div class="product_content">
								<div class="product_image">
									<a href="{{ URL::to($restaurant->slug.'/'.$product->slug) }}">
									@if($product->image_web != null)
										{{ HTML::imagepf($product->image_web) }}
									@else
										{{ HTML::imagepf($restaurant->landing_page->logo) }}
									@endif
									</a>
								</div>
								<div class="product_info" style="padding: 5px;">
									<p>{{ $product->product }}</p>
									<p>{{ '$ '.$product->value }}</p>
									<p>
										<a href="{{ URL::to($restaurant->slug.'/'.$product->slug) }}" class="btn btn-primary btn-sm">Agregar al carrito</a>
									</p>
								</div>
							</div>
						</section>
					<!--</a>-->
				</div>
			@endforeach
			</div>
		</div>
	</div>
</div>

<script>
	function procesar(){
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