@extends('general.single_page')
@section('fContent')
<nav class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
            data-target=".navbar-ex1-collapse">
     	<i class="fa fa-bars fa-2x"></i>
    </button>
  </div>
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Inicio <b class="caret"></b></a>
			<ul class="dropdown-menu">
                @if($promociones >= 1)<li><a href="{{ URL::to('promociones') }}">Promociones</a></li>@endif
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
    <ul class="nav navbar-nav navbar-right">
		@if(Auth::check())
			<li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
            <!-- <li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li> -->
            @include('../include/linckChat')
		@else
			<li><a href="{{ URL::to('login') }}"> Iniciar sesión</a></li>
			<li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}"> Registrarse</a></li>
		@endif
    </ul>
  </div>
</nav>
<div class="container below_bar white_content">
	<div class="row">
		<div class="col-sm-6 text-left sm-text-center">
			<p><strong>Disponible en:</strong></p>
			<a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil" target="_blank">
				{{ HTML::image('https://developer.android.com/images/brand/es-419_generic_rgb_wo_45.png')}}
			</a>
			<a href="https://itunes.apple.com/us/app/pidafacil-comida-domicilio/id990772385" target="_blank">{{ HTML::image('images/icons/appstore.svg')}}</a>
		</div>
		<div class="col-sm-6 text-right hide_small">
			<p><strong>Estamos en:</strong></p>
			<a href="https://twitter.com/pidafacil">{{ HTML::image('images/socials/twitter64.png', 'twitter pidafacil', array('width' => '32px')) }}</a>
			<a href="https://www.facebook.com/pidafacilsv" target="_blank">{{ HTML::image('images/socials/facebook64.png', 'facebook pidafacil', array('width' => '32px')) }}</a>
			<a href="https://instagram.com/pidafacil">{{ HTML::image('images/socials/instagram64.png', 'instagram pidafacil', array('width' => '32px')) }}</a>
		</div>
	</div>
	<div class="center_content">
		{{ HTML::image('images/logo.svg', 'pidafacil', array('class' => 'main_logo')) }}
		<div class="space">
			<div class="form-group">
				<div class="input-group ui-widget">
    				<label for="tags" class="input-group-addon red-label">
    					<i class="fa fa-search"></i><span class="hide_small"> Buscar:</span>
    				</label>
    				<input id="tags" type="text" class="form-control searchTags" placeholder="Pizza, Hamburguesa, Los Pollos Hermanos, Cafeter&iacute;a El Buen Tomar, etc." />
    			</div>
			</div>
		</div>
		<div class="space row">
            @if($promociones <= 0)

                <div class="modes col-md-2 col-lg-2 col-sm-2 col-xs-12"></div>
            @endif
			<div class="modes col-md-4 col-lg-4 col-sm-4 col-xs-12">
				<section class="center_content">
					<a href="{{ URL::to('explorar') }}">
						{{ HTML::image('images/icons/explorar.png', 'Explorar', array('width' => '128px'))}} <br>
						<h3>Explorar</h3>
					</a>
					<div class="hide_small">
						<p>Descubre diferentes restaurantes y <br />platillos que tenemos para ofrecerte.</p>
						<!--<a href="{{ URL::to('explorar') }}"><button class="btn btn-primary">Ir a Explorar</button></a>-->
					</div>
				</section>
			</div>
            @if($promociones >= 1)
                <div class="modes col-md-4 col-lg-4 col-sm-4 col-xs-12">
                    <section class="center_content">
                        <a href="{{ URL::to('promociones') }}">
                            {{ HTML::image('images/icons/promociones.png', 'Promociones', array('width' => '128px')) }}
                            <h3>Promociones</h3>
                        </a>
                        <div class="hide_small">
                            <p>Disfruta las mejores promociones y <br />ofertas para ti.</p>
                            <!--<a href="{{ URL::to('promociones') }}"><button class="btn btn-primary">Ir a Promociones</button></a>-->
                        </div>
                    </section>
                </div>
            @endif
			<div class="modes col-md-4 col-lg-4 col-sm-4 col-xs-12">
				<section class="center_content">
					<a href="{{ URL::to('user/orders') }}">
						{{ HTML::image('images/icons/repetir.png', 'Repetir pedido', array('width' => '128px')) }}
						<h3>Repetir pedido</h3>
					</a>
					<div class="hide_small">
						<p>Escoge platillos de tus &uacute;ltimos pedidos, <br />sin necesidad de volver a navegar.</p>
					 	<!--<a href="{{ URL::to('user/orders') }}"><button class="btn btn-primary">Ir a Repetir pedido</button></a>-->
					 </div>
				</section>
			</div>
		</div>
	</div>
</div>
@stop
