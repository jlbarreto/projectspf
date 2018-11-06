@extends('general.master_page')
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
            <!-- <li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li> -->
            @include('../include/linckChat')
		@else
			<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
			<li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
		@endif
    </ul>
  </div>
</nav>
<div class="container below_bar white_content">
	<div class="center_content" id="login">
		
		<div class="row">
			<div class="col-sm-6">
				<div class="types">
			
					<h1>Iniciar sesión</h1>
					<p>Ingresa a través de Facebook, Google + o cuenta de correo.</p>
					<div class="row space_15">
						<div class="col-sm-8 col-sm-offset-2" id="facebook">
							<a class="btn btn_fb btn-block" href="{{ URL::to('/login/fbauth') }}"><img src="" alt="">Ingresa con Facebook</a>
						</div>
					</div>
					<div class="row space_15" >
						<div class="col-sm-8 col-sm-offset-2 social_media_google" id="google">
							<a class="btn btn_go btn-block" href="{{ URL::to('/login/gauth') }}"><img src="" alt="">Ingresa con Google +</a>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-8 col-sm-offset-2 space_50">
							{{ Form::open(array('doLogin','post', 'role' => 'form')) }}
								<div class="form-group">
									{{ Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email')) }}
								</div>
								<div class="form-group">
									{{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
								</div>
								<div class="checkbox">
									<label for="">
										{{ Form::checkbox('stay-logged', 1,['class'=>'checkbox no_align']) }} Recordar mi contraseña
									</label>
								</div>
								<p class="help-block"><a href="{{ URL::to('password/remind') }}">&iquest;Has olvidado tu contrase&ntilde;a?</a></p>
								{{ Form::submit('Login', array('class' => 'btn btn-primary button_150')) }}
							{{ Form::close() }}	
						</div>
					</div>

				</div>
			</div>
			<div class="col-sm-6">
				<div id="signup" class="text-left" style="padding: 0px 1em;">
					<h1>Registrarse</h1>
					<a name="signup"></a> 
					<p>Reg&iacute;strate y forma parte de la comunidad de pedidos más grande de El Salvdor.</p>
					<p>Tan fácil como completar el siguiente formulario:</p>
					{{ Form::open(array('url'=>'register','method' => 'post')) }}
						@if(count($errors->all()) > 0)
							<div class="order order_despachada">
								{{ HTML::ul($errors->all()) }}
							</div>
						@else
						@endif
						{{ Form::text('name', Input::old('name'), array('placeholder' => 'Nombre', 'class' => 'form-control space', 'required')) }}
						{{ Form::text('last_name', Input::old('last_name'), array('placeholder' => 'Apellido', 'class' => 'form-control space_15')) }}
						{{ Form::email('email', Input::old('email'), array('placeholder' => 'Email', 'class' => 'form-control space_15', 'required')) }}
						{{ Form::password('password', array('placeholder' => 'Contraseña', 'class' => 'form-control space_15', 'required')) }}
						{{ Form::password('password_confirm', array('placeholder' => 'Confirmar contraseña', 'class' => 'form-control space_15', 'required')) }}
						{{ Form::text('phone', Input::old('phone'), array('placeholder' => 'Télefono', 'class' => 'form-control space_15', 'required')) }}
						<label class="space_15"> {{ Form::checkbox('terms_acceptance', 1, 1) }} Acepto los <a class="red_link" href="#">términos y condiciones</a></label>
						<br>
						{{ Form::submit('Registrate', array('class' => 'btn btn-default button_150 space')) }}	
					{{ Form::close() }}
				</div>
			</div>
		</div>

	</div>
</div>
@stop