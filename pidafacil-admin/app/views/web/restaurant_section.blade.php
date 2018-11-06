@extends('general.restaurant_info_')
@section('restaurant')

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
<div class="container">
	<div class="col-md-8 col-xs-12">
		<div class="row">
			<div class="col-md-6 col-xs-12 to-rest" align="center">
				<a href="{{ URL::to($restaurant->slug) }}">{{ HTML::image($landingpage->logo,'',array('style'=>'height: 100px;')) }}<br/><strong>Ir a Restaurante</strong></a>
			</div>
			<div class="col-md-6 col-xs-12 white_content" align="center">
				<span class="hide_small"><h1>{{ $restaurant->name }}</h1></span>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-xs-12">
		<div class="col-xs-12 rest-menu">
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
			</div>
		</div>
	</div>
</div>
@stop
@section('content')
<div class="container gray_content">
	<div class="row">
		<h1 class="text-left sec-title"><a href="{{ URL::to($restaurant->slug) }}">&lt; {{ $section->section }}</a></h1>
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
										{{ HTML::image($product->image_web) }}
									@else
										{{ HTML::image($restaurant->landing_page->logo) }}
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
@stop

<!--
<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">		
	<a href="{{ URL::to($restaurant->slug.'/'.$product->slug) }}">
		<section class="well well-lg product_image">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-4">
				@if($product->image_web != null)
					{{ HTML::image($product->image_web) }}
				@else
					{{ HTML::image($restaurant->landing_page->logo) }}
				@endif
			</div>
			<h4>{{ $product->product }}</h4>
		</section>
	</a>
</div>
-->