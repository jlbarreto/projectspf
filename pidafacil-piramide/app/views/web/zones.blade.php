@extends('general.general_white')
@section('content')
<style>
    .title{
        font-size: 18px;
        margin: 20px 0;
        padding: 4px;
    }
</style>
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
<div class="container white_content" style="padding-bottom: 1em;">
	<h1 class="text-center">Zonas de cobertura</h1>
        <div class="row text-center types" id="zones">
            <div class="col-md-6">
                <div class="title">
                    ZONA 1 <br/>
                    $3.00
                </div>
                <div class="col-md-6">Maquilishuat</div>
                <div class="col-md-6">La Mascota</div>
                <div class="col-md-6">San Benito</div>
                <div class="col-md-6">Escalón</div>
                <div class="col-md-6">Campestre</div>
                <div class="col-md-6">Altamira</div>
                <div class="col-md-6">Cumbres de Cuscatlán</div>
                <div class="col-md-6">San Francisco</div>
                <div class="col-md-6">La Sultana</div>
                <div class="col-md-6">General Arce</div>
                <div class="col-md-6">Lomas de San Francisco</div>
                <div class="col-md-6">Miramonte</div>
                <div class="col-md-6">San Mateo</div>
                <div class="col-md-6">Santa Elena</div>
                <div class="col-md-6">Miralvalle</div>
                <div class="col-md-6">Residencial Escalonia</div>
                <div class="col-md-6">Colonia Escalon Norte</div>
                <div class="col-md-6">Residencial Claudia</div>
                <div class="col-md-6">Urbanización Santa Mónica</div>
                <div class="col-md-6">Colonia la sultana</div>
                <div class="col-md-6">Reparto los héroes</div>
                <div class="col-md-6">Plan de la laguna</div>
                <div class="col-md-6">Altos de miralvalle</div>
                <div class="col-md-6">Merliot</div>
            </div>
            <div class="col-md-3">
                <div class="title">
                    ZONA 2 <br/>
                    $4.00
                </div>
                <div class="col-md-12">Santa Tecla</div>
                <div class="col-md-12">Flor Blanca</div>
                <div class="col-md-12">San Luis</div>
                <div class="col-md-12">Montebello</div>
                <div class="col-md-12">Vía del Mar</div>
                <div class="col-md-12">Cima I, II, III y IV</div>
                <div class="col-md-12">Residencial Decapolis</div>
                <div class="col-md-12">Colonia Yumuri</div>
                <div class="col-md-12">Urbanización Toluca</div>
                <div class="col-md-12">Colonia Ávila</div>
                <div class="col-md-12">Metrocentró</div>
                <div class="col-md-12">Ciudad satélite</div>
                <div class="col-md-12">Vista Hermosa</div>
            </div>
            <div class="col-md-3">
                <div class="title">
                    ZONA 3 <br/>
                    $5.00
                </div>
                <div class="col-md-12">Las Piletas</div>
                <div class="col-md-12">Tuscania</div>
                <div class="col-md-12">Quintas de Santa Elena</div>
                <div class="col-md-12">Villa Bosque</div>
                <div class="col-md-12">Alturas de Tenerife</div>
                <div class="col-md-12">Alameda Juan Pablo II</div>
                <div class="col-md-12">Centro de Gobierno</div>
                <div class="col-md-12">Villa Veranda</div>
                <div class="col-md-12">Condado Santa Rosa</div>
                <div class="col-md-12">Colonia Médica</div>
                <div class="col-md-12">Colonia de las delicias</div>
                <div class="col-md-12">Pinares de Suiza</div>
            </div>
	</div>
  <p style="margin-top: 1.5em;">
    * Ubicaciones fuera de las arriba mencionadas favor consultar previamente.
  </p>
  <p>
    * Nos reservamos el derecho de excluir lugares específicos aunque pertenezcan a nuestras zonas de cobertura. Favor consultar previamente.
  </p>
</div>
@stop
