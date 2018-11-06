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
						<p>  {{ $product->description }} </p>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="col-sm-6 col-xs-12">
		<div class="types">
			{{ Form::open(array('url'=>'cart/add','method'=>'post', 'onsubmit'=>'return addToCart();', 'id'=>'frmAddPrd', 'class' => 'create_order', 'role' => 'form', 'data-toggle' => 'validator')) }}
			{{ Form::hidden('product_id', $product->product_id, array('id' => 'product_id')) }}
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
                                    <button type="submit" id="ajax" class="btn btn-default btn-lg">Agregar al carrito</button>
					<div class="modal text-left" id="add" tabindex="-1" role="dialog" aria-labelledby="">
						<div class="modal-dialog">
							<div class="modal-content">
								<div  class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				    				<h4 class="modal-title" id="myModalLabel">¡Gracias por su compra!</h4>
								</div>
								<div class="modal-body">
									<p id="validate"></p>
								</div>
								<div class="modal-footer">
									<a href="{{ URL::to($restaurant->slug) }}" class="btn btn-default">Seguir comprando</a>
									<a href="{{ URL::to('cart') }}" class="btn btn-default">Ir al carrito</a>
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
							<ul><li class="white_content">Este producto no tiene ingredientes removibles</li></ul>
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
			{{ Form::close() }}
		</div>
		</div>
	</div>
</div>
<script>
    function addToCart(){
        appboy.logCustomEvent("Add To Cart");
        
        return true;
    }
</script>
@stop
