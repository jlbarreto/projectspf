@extends('general.restaurant_info_')
@section('restaurant')
<?php
foreach($landingpage as $key => $value):
	$logo = $value->logo;
	$banner = $value->banner; 
	$header = $value->header;
	$slogan = $value->slogan;
	$title_1 = $value->title_1;
	$title_2 = $value->title_2;
	$title_3 = $value->title_3;
	$text_1 = $value->text_1;
	$text_2 = $value->text_2;
	$text_3 = $value->text_3;
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
                    <!-- <li><a href="{{ URL::to('promociones') }}">Promociones</a></li> -->
				<li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
				<li><a href="{{ URL::to('explorar') }}">Explorar</a></li>
				<li class="divider"></li>
				<li><a href="{{ URL::to('cart') }}">Carrito</a></li>
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
            <!-- <li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li>-->
            @include('../include/linckChat')
		@else
			<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
			<li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
		@endif
    </ul>
  </div>
</nav>
<div class="container" style="padding-bottom: 25px;">
	<div class="col-lg-9 col-xs-12">
		<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="center">
				<a href="{{ URL::to($restaurant_info->slug) }}">{{ HTML::imagepf($logo,'',array('style'=>'height: 100px;')) }}</a>
			</div>
			<div class="col-lg-8 col-md-4 col-sm-8 col-xs-9 white_content" align="center">
				<span class="hide_small"><h1 style="font-size: 50px;">{{ $restaurant_info->name }}</h1></span>
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
					  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::to($restaurant_info->slug .'/sections/'.$val->section_id) }}">{{ $val->section }}</a></li>
					  @endforeach
					  	<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ URL::to($restaurant_info->slug .'/promociones') }}">Promociones</a></li>
					  </ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
@section('content')
<div class="container white_content types" style="padding-bottom: 1em;">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1>{{ $title_1 }}</h1>
			<p>{{ $text_1 }}</p>
			<!--<h1>{{-- $title_2 --}}</h1>
			<p>{{-- $text_2 --}}</p>
			<h1>{{-- $title_3 --}}</h1>
			<p>{{-- $text_3 --}}</p>-->
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h1>Sucursales</h1>
			<ul>
				@foreach($sucursales as $sucursal)
				<li>
                                    <h4><strong>{{ $sucursal->name }}</strong>: {{ $sucursal->address }}</h4>
				</li>
                                @endforeach
			</ul>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h1>Horarios</h1>
                        <table class="table text-center">
                            <tr>
                                <th></th>
                                @foreach($service_types as $service_type)
                                <th colspan="2"class="text-center">{{ $service_type->service_type }}</th>
                                @endforeach
                            </tr>
                            @foreach($arr_dias as $k=>$dia)
                            <tr>
                                <th>{{ $dia }}</th>
                                @foreach($service_types as $service_type)
                                <td>{{ date("g:ia", strtotime($schedules[$k][$service_type->service_type_id]['opening'])) }}</td>
                                <td>{{ date("g:ia", strtotime($schedules[$k][$service_type->service_type_id]['closing'])) }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </table>
		</div>
	</div>
<!--	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h1>Contactos</h1>
			<p>Tel. {{ $restaurant_info->phone }}</p>
		</div>
	</div>-->
</div>
@stop