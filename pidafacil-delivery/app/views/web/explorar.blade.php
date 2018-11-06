@extends('general.general_white')
@section('content')
<nav class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
            data-target=".navbar-ex1-collapse">
     	<i class="fa fa-bars fa-2x"></i>
    </button>
	<a class="navbar-brand" href="{{ URL::to('/') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
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
<div class="container-fluid center_content white_content">
@if(isset($restaurants))
	<!--<div class="row hide_small">
		<div class="container">
			<h1>Ay&uacute;date con nuestros filtros</h1>
		</div>
	</div>-->
	<div class="row">
		<div class="container">
			<div class="filter-bar">
			@include('include.filters')
			</div>
		</div>
	</div>
	<div class="container">
		<div style="text-align:left;">
			<h2>{{ $tag->tag_name }}</h2>
			<!--<p>Se encontraron <span class="red">{{-- $restaurants->count() --}}</span> restaurante(s).</p>-->
		</div>
		<div class="row space_15" style="padding-bottom: 1em;">
			<?php $i = 0; ?>
			@foreach($restaurants as $key => $value)
				<?php $i += 1; ?>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 promo_rest space_15">
					<a href="{{ URL::to($value->slug) }}">
						<section style="background-image: url({{ URL::to($value->landing_page['header']) }});">
							{{ HTML::image( $value->landing_page['logo']) }}
						</section>
					</a>
				</div>
			@endforeach
			@if($i == 0)
				<p>Actualmente no hemos asignado restaurantes a esta secci&oacute;n, intenta con otra opci&oacute;n de la barra superior.</p>
			@endif
		</div>
	</div>
@else
	<div class="container">
		<div class="row">
			<div class="food col-md-6 col-xs-12" id="food-type">
				<h1>Tipos de comida</h1>
				@if(isset($ftypes))
					<div class="row">
						<div class="col-xs-12">
							<div class="types">
							<div class="row">
					@foreach($ftypes as $k => $val)
						<div class="col-xs-4 col-sm-3">
							<a href="{{ URL::to('explorar/'.$val->tag_name) }}" 
								style="text-decoration: none; display: block; padding: 0.5em 0px;">
							{{ HTML::image('images/tags/'.$val->tag_id.'.png') }} <br />
							{{ $val->tag_name }}
							</a>
						</div>
					@endforeach
							</div>
							</div>
						</div>
					</div>
				@endif
				<p style="padding-top: 1em;">Conoce las diferentes categorías que te ofrecemos, encontrarás una gran variedad de restaurantes y platillos favoritos, desde lo más popular hasta lo que no le has dado la oportunidad de conocer.</p>
			</div>
			<div class="mood col-md-6 col-xs-12">
				<h1>Tus ocasiones</h1>
				@if(isset($fmoods))
					<div class="row">
						<div class="col-xs-12">
							<div class="types">
							<div class="row">
					@foreach($fmoods as $k => $val)
						<div class="col-xs-4 col-sm-3">
							<a href="{{ URL::to('explorar/'.$val->tag_name) }}" 
								style="text-decoration: none; display: block; padding: 0.5em 0px;">
							{{ HTML::image('images/tags/'.$val->tag_id.'.png') }}
							{{ $val->tag_name }}
							</a>
						</div>
					@endforeach
							</div>
							</div>
						</div>
					</div>
				@endif
				<p style="padding-top: 1em;">&iquest;Estás celebrando un evento en particular? Navega en las diferentes opciones que hemos pensado para ti y encontrarás la mejor opción de comida.</p>			
			</div>
		</div>
	</div>
@endif
@stop