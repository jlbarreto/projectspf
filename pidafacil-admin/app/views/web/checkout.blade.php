@extends('general.general_white')
@section('content')
<?php 
	$total = Session::get('total_order');
	$cart = Session::get('cart');
?> 

<?php 
	$timezone = date_default_timezone_get();
	$date = date('G:i:s', time());
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
<div class="container white_content" style="padding-bottom: 1em;">
	<h1 class="text-center">Estas a dos pasos de completar tu orden</h1>
	
	@if($errors->has())
	<div class="alert alert-danger" role="alert">
		<ul style="list-style-type: square;">
		@foreach ($errors->all() as $error)
				<li style="list-style: square;">{{ $error }}</li>
		@endforeach
		</ul>
	</div>
	@endif
	
	<div class="row">

		<div class="col-md-9">
			<div class="types table-responsive">
				@if(isset($cart) && count($cart) > 0)
					@foreach($cart as $key=>$product)
						@if(is_array($product))
						<table class="table">
					      <caption><h3 style="color: #FFF;">Detalle de tu orden.</h3></caption>
					      <thead>
					        <tr>
					          <th>Qty.</th>
					          <th>Producto</th>
					          <th>Condiciones</th>
					          <th>Ingredientes</th>					          
					          <th>Observaciones</th>
					          <th>Precio</th>
					          <th>Total</th>
					        </tr>
					      </thead>
					      <tbody>
							@foreach($product as $val=>$pro)
							<tr>
					          <th scope="row">{{ $pro['quantity'] }}</th>
					          <td>
					          	<strong>{{ $pro['product'] }}</strong><br/>
					          	{{ $pro['description'] }}
					          </td>
					          <td nowrap="nowrap">
					          	@if($pro['conditions'] != null)
									@foreach($pro['conditions'] as $condi)
										{{ $condi['condition_condition'] }}:<br/>
										<strong>{{ $condi['condition_option'] }}</strong><br/>
									@endforeach
								@else
									No aplica.
								@endif
					          </td>
					          <td nowrap="nowrap">
					          	@if($pro['ingredients'] != null)
									@foreach($pro['ingredients'] as $ingre)
										{{ ($ingre['active'] == 1 ? 'Con ' : 'Sin ') . $ingre['ingredient'] }}<br/>
									@endforeach
								@else
									No aplica.
								@endif
					          </td>
					          <td>{{ $pro['comment'] }}</td>
					          <td class="text-right">$&nbsp;{{ number_format($pro['unit_price'],2) }}</td>
					          <td class="text-right"><strong>$&nbsp;{{ number_format($pro['total_price'],2) }}</strong></td>
					        </tr>
							@endforeach
						  </tbody>
						  <tfoot>
						  	<tr>
						  		<td colspan="7" class="text-right"><strong>Total a pagar: ${{ $total }}</strong></td>
						  	</tr>
						  </tfoot>
						</table>
						@else
						@endif
					@endforeach
				@else
				@endif
			</div>
		</div>

		<div class="col-md-3 detail gray_content">
			<?php $show_type2 = 0; ?>

			{{ Form::open(array('url' => 'order/create','method' => 'POST', 'class' => 'checkout_order')) }}

					<h2>1. Tipo de servicio</h2>
					@foreach($schedule as $key=>$value)
						@if($value->service_type_id == 1)
							@if($date > $value->opening_time && $date < $value->closing_time)
								<div style="border-bottom: 1px solid #999;" >
									<div class="radio input-lg">
										<label for="service_type_1">
											{{ Form::radio('service_type_id', '1', null,['id' => 'service_type_1', ]) }}
											A domicilio
										</label>
									</div>
									<div id="user-address" style="display: none; padding: 0px 1em;">
										<div class="form-group">
											@if(is_array($usr_address) && count($usr_address) > 0)
												<label for="selType1">Escoge tú dirección de destino:</label>
												<select name="address_id" id="selType1" class="form-control input-lg">
												    <option selected disabled>Selecciona tú dirección</option>
												    @foreach($usr_address as $key => $value)
												    <option value="{{ $key }}">{{ $value }}</option>
												    @endforeach
												</select>
										</div>
										<div class="form-group">
												<label>O ingresa una nueva dirección:</label>
												<button type="button" class="btn btn-primary active" id="new-address" data-toggle="modal" data-target="#address">Ingresa una nueva dirección</button>
											@else
												<h4 class="text-warning">No existe ningún registro de direcciones</h4>
												<p>Da click aqui para crear una nueva dirección</p>
												<button type="button"  id="new-address" data-toggle="modal" data-target="#address">Ingresa una nueva dirección</button>
											@endif
										</div>
									</div>	
								</div>
							@else
								<?php $show_type2 = 1; ?>
							@endif
						@elseif($value->service_type_id == 2)
							<div style="border-bottom: 1px solid #999;">
								<div class="radio input-lg">
									<label for="service_type_2">
										{{ Form::radio('service_type_id', '2', null,['id' => 'service_type_2']) }}
										Para llevar
									</label>
								</div>
								<div id="rest-address" style="display: none; padding: 0px 1em;">
									<div class="form-group">
										<label for="selType2">Escoge la dirección del restaurante:</label>
										<select name="restaurant_address" id="selType2" class="form-control input-lg">
											<option selected disabled>Selecciona un restaurante</option>
											@foreach($res_address as $key => $val)
											<option value="{{ $key }}">{{ $val }}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label>Determina una hora para recoger tu pedido:</label>
										<?php
											$otime = strtotime($value->opening_time);
											$oint = date('G', $otime);
											$ctime = strtotime($value->closing_time);
											$cint = date('G', $ctime);
										?>
										<div class="row">
											<div class="col-xs-6">
												<select name="hour" class="form-control input-lg">	
													@for($i=$oint; $i < $cint; $i++)
														<option value="{{ $i }}">{{ $i }}</option>
													@endfor
												</select>
											</div>
											<div class="col-xs-6">
												<select name="minutes" class="form-control input-lg">
													<option disabled>mm</option>
													<option value="00">00</option>
													<option value="05">05</option>
													@for($i = 10; $i < 60; $i+=5 )
														<?php $minutes = array($i) ?>
														@foreach($minutes as $key=>$value)
															<option value="{{ $value }}">{{ $value }}</option>
														@endforeach
													@endfor
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						@endif
					@endforeach

						<div class="payment-method">
							<h2>2. Método de pago</h2>
							<div style="border-bottom: 1px solid #999;">
								<div class="radio input-lg">
									<label for="method_1">
										{{ Form::radio('payment_method_id', '2', null,['id' => 'method_1']) }}
										Tarjeta de cr&eacute;dito
									</label>
								</div>
								<div id="credit-card" style="display: none; padding: 0px 1em;">
									<div class="form-group">
										<p class="help-block">Campos marcados con * son obligatorios</p>
										{{ Form::text('user_credit', null, array('placeholder' => '* Titular de la tarjeta', 'required', 'class' => 'form-control credit_required')) }}
									</div>
									<div id="cc_frm_grp" class="form-group has-error has-feedback">
										<div class="input-group">
											<span class="input-group-addon" id="inp_grp_add">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
											<input type="text" class="form-control" aria-describedby="inputGroupSuccess1Status" name="credit_card" placeholder="* Número de tarjeta" id="credit_card">
										</div>
										<span id="cc_stat_icon" class="fa fa-exclamation fa-lg form-control-feedback" aria-hidden="true" style="top: 10px;"></span>
									</div>
									<div id="ccv_grp" class="form-group has-error has-feedback">
								        <input name="secure_code" placeholder="* Código de seguridad" class="form-control" id="inp_ccv" aria-describedby="inputSuccess2Status" type="text">
								        <span id="ccv_icon" class="fa fa-exclamation fa-lg form-control-feedback" aria-hidden="true" style="top: 10px;"></span>
								    </div>		
									<div class="form-group">
										<label>Fecha de vencimiento:</label>
										<div class="row">
											<div class="col-xs-6">
												{{ Form::select('month',[
													null => 'Mes', 
													1 => 'Enero',
													2 => 'Febrero',
													3 => 'Marzo',
													4 => 'Abril',
													5 => 'Mayo',
													6 => 'Junio',
													7 => 'Julio',
													8 => 'Agosto',
													9 => 'Septiembre',
													10 => 'Octubre',
													11 => 'Noviembre',
													12 => 'Diciembre'], null, array('class'=>'form-control input-lg')) }}
											</div>
											<div class="col-xs-6"><?php
												$ystart = date('Y');
												$exp_years = array();
												for($y = $ystart; $y <= ($ystart+20); $y++){
													$exp_years[$y] = $y; 
												}
												$exp_years = array_add($exp_years, '', 'Año');
												ksort($exp_years); ?>
												{{ Form::select('year', $exp_years, null, array('class'=>'form-control input-lg')) }}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div style="border-bottom: 1px solid #999;">
								<div class="radio input-lg">
									<label for="method_2">
										{{ Form::radio('payment_method_id', '1', null,['id' => 'method_2']) }}
										Efectivo
									</label>
								</div>
								<div id="cash" style="display: none; padding: 0px 1em;">
									<div class="form-group">
										<p class="help-block">* Recuerde que debe ingresar una cantidad mayor al total de su orden</p>
										<select id="cash-select" name="cash" class="form-control input-lg">
											<option selected disabled="true">Agregue una cantidad</option>
											@for($i = 5; $i <=300; $i+=5)
											<option value="{{ $i }}"  <?= ($i < $total ? 'disabled' : '') ?>>{{ $i }}</option>
											@endfor
										</select>
									</div>
									<label>El total a pagar es: $ {{ number_format( $total, 2) }}</label>
									<p id="cambio">Su cambio es de: {{ $total }}</p>
								</div>
							</div>
						</div>
						<div class="text-center" style="padding-top: 1em;">
							{{ Form::submit('Comprar', array('class'=>'btn btn-default btn-lg')) }}
						</div>
					
			{{ Form::close() }}
		</div>

	</div>
	<div class="modal fade gray_content" id="address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		{{ Form::open(array('url' => 'user/address/create', 'method' => 'POST', 'class' => 'new-address', 'id' => 'address-form')) }}
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4>Nueva dirección</h4>
				</div>
				<div class="modal-body">
					<p>Completa el siguiente formulario para agregar una nueva dirección</p>
					<p class="bullet">Campos marcados con * son obligatorios</p>
					<br>
					<div class="form-group new_add_modal">
						{{ Form::text('address_name', null, array('placeholder' => '* Nombre de registro ej. Mi casa...', 'required' => 'required', 'class'=>'form-control space_15')) }}
						{{ Form::text('address_1', null, array('placeholder' => '* Dirección', 'required' => 'required', 'class'=>'form-control space_15')) }}
						{{ Form::text('address_2', null, array('placeholder' => 'Complemento de dirección', 'class'=>'form-control space_15')) }}
						{{ Form::text('city', null, array('placeholder' => '* Ciudad/Municipio', 'required' => 'required', 'class'=>'form-control space_15')) }}
						{{ Form::text('state', null, array('placeholder' => 'Estado/Departamento', 'required' => 'required', 'class'=>'form-control space_15')) }}
						{{ Form::text('reference', null, array('placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'form-control space_15')) }}
					</div>
				</div>
				<div class="modal-footer" >
					{{ Form::submit('Agregar', array('class'=>'btn btn-default button_150')) }}
				</div>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>
<script type="text/javascript">
	$("#service_type_1").click(function(){
			$("#user-address").show();
			$("#rest-address").hide();
	});

	$("#service_type_2").click(function(){
			$("#rest-address").show();
			$("#user-address").hide();
	});
	
	$("#method_1").click(function(){
			$("#credit-card").show();
			$('.credit_required').attr('required','true')
			$("#cash").hide();
			$(".cash_required").removeAttr('required','true')
	});

	$("#method_2").click(function(){
			$("#cash").show();
			$('.cash_required').attr('required','true')
			$("#credit-card").hide();
			$(".credit_required").removeAttr('required','true')
	});

	$( "#cash-select" ).change(function() {
		var str = "";
		$("#cash-select option:selected").each(function(){
			var s = $(this).text();
			var t = {{ $total }};
			if(s > 0){
				var nt = s - t;
				str += "Su cambio será de: $ <strong>" + nt.toFixed(2) + "</strong>";
			}else{
				str += "Su cambio será de: <strong>$ 0.00</strong>";
			}
		});
		$("#cambio").html(str);
		//alert( "Handler for .change() called." );
	}).change();
	
	var ccv_n = 0;
	$(function() {
        $('#credit_card').validateCreditCard(function(result) {
        	$('#showmsg').html('Card type: ' + (result.card_type == null ? '-' : result.card_type.name)
                     + '<br>Valid: ' + result.valid
                     + '<br>Length valid: ' + result.length_valid
                     + '<br>Luhn valid: ' + result.luhn_valid);
        	if(result.card_type != null){
        		$('#inp_grp_add').addClass(result.card_type.name);
        		if(result.card_type.name == 'amex'){
        			ccv_n = 4
        			$('#inp_ccv').attr("maxlength", ccv_n);        			
        		}else{
        			ccv_n = 3
        			$('#inp_ccv').attr("maxlength", ccv_n);
        		}
        		if(result.valid == true){
        			$('#cc_frm_grp').removeClass('has-error');
        			$('#cc_frm_grp').addClass('has-success');
        			$('#cc_stat_icon').removeClass('fa-exclamation');
        			$('#cc_stat_icon').addClass('fa-check');
        		}else{
        			$('#cc_frm_grp').removeClass('has-success');
        			$('#cc_frm_grp').addClass('has-error');
        			$('#cc_stat_icon').removeClass('fa-check');
        			$('#cc_stat_icon').addClass('fa-exclamation');
        		}
        	}else{
        		$('#inp_grp_add').removeClass();
        		$('#inp_grp_add').addClass('input-group-addon');
        		$('#inp_ccv').attr("maxlength", -1);
        	}
        }, { accept: ['visa', 'mastercard', 'amex'] });
    });

	$("#inp_ccv").keyup(function() {
		this.value = this.value.replace(/[^0-9\.]/g,'');
		var ln = $(this).val().length;
		if(ln == ccv_n){
			$('#ccv_grp').removeClass('has-error');
			$('#ccv_grp').addClass('has-success');
			$('#ccv_icon').removeClass('fa-exclamation');
			$('#ccv_icon').addClass('fa-check');
    	}else{
    		$('#ccv_grp').removeClass('has-success');
			$('#ccv_grp').addClass('has-error');
			$('#ccv_icon').removeClass('fa-check');
			$('#ccv_icon').addClass('fa-exclamation');
    	}
	});
</script>
@stop