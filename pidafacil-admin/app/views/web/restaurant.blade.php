@extends('general.restaurant_master')
@section('restaurant')
<?php
foreach($landingpage as $key => $value):
	$logo = $value->logo;
	$banner = $value->banner; 
	$header = $value->header;
endforeach; 

?>
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
<div class="container center_content" style="padding:25px;">
	<div class="row">
		<div class="col-lg-12" style="margin-bottom: 1em;">
			{{ HTML::image($logo) }}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12  white_content" align="center">
			<div style="background-color:rgba(0,0,0,.5); padding:25px;">
			@foreach($sections as $key=>$section)
				<a href="{{ URL::to($informacion->slug.'/sections/'.$section->section_id) }}" class="btn btn-primary button_200 space_15">{{ $section->section }}</a> 
			@endforeach
				<a href="{{ URL::to($informacion->slug.'/promociones') }}" class="btn btn-default button_200 space_15">Promociones</a>
			</div>
		</div>
	</div>
	<div class="row space	" align="center">
		<a href="{{ $informacion->slug }}/about" class="btn btn_white button_150" style="color: #33333;">Más información</a>
	</div>
</div>
@stop
 