@extends('general.single_page')
@section('fContent')
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
		@else
			<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
			<li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
		@endif
    </ul>
  </div>
</nav>
<div class="container below_bar white_content">
	<div class="center_content">
		<h1>Hola, {{ Auth::user()->name .' '. Auth::user()->last_name }}</h1>
		@if(!empty(Auth::user()->photo))
			{{ HTML::image( Auth::user()->photo, 'profile-picture', array('class' => 'img-circle profile_picture space')) }}
		@else
			<div class="img-circle profile_default" style="background-image:url('images/user_bf.png'); width:175px; height: 175px; margin:0 auto;">
				<?php 
					$nombre = substr(Auth::user()->name, 0, 1);
					$apellido = substr(Auth::user()->last_name, 0, 1);
				?>
				<h1>{{ $nombre.$apellido }}</h1> 
			</div>
			
			<!--
			{{ HTML::image('images/user_bf.png', 'user', array('class' => 'img-circle profile_picture space')) }}
			<?php 
				$nombre = substr(Auth::user()->name, 0, 1);
				$apellido = substr(Auth::user()->last_name, 0, 1);
			?>
			<h1 style="color:gray; ">{{ $nombre.$apellido }}</h1> -->
		@endif
		<a href="edit"><h2>{{ Auth::user()->name .' '. Auth::user()->last_name }}</h2></a>
		<p>{{ Auth::user()->email }}</p>
		<div class="actions space">
			<a class="btn btn-primary button_200 user_actions" href="#">Mis Favoritos</a>
			<a class="btn btn-primary button_200 user_actions" href="{{ URL::to('user/address') }}">Mis Direcciones</a>
			<a class="btn btn-primary button_200 user_actions" href="#">Mis Comentarios</a>
			<a class="btn btn-primary button_200 user_actions" href="{{ URL::to('user/orders') }}">Mis Pedidos</a>
		</div>
		<a class="btn btn-link button_150 space_50" href="{{ URL::to('logout') }}">Cerrar sesión</a>
	</div>
</div>
@stop