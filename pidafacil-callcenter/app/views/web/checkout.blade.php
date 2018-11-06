@extends('general.visor_template')
@section('content')

<?php
  $total = Session::get('total_order');
  $cart = Session::get('cart');
  $cantidad = Session::get('cart2');

  #echo $contador_add;
  $timezone = date_default_timezone_get();
  $date = date('G:i:s', time());  
?>

<div class="container" style="padding-bottom: 1em;">
  	<h1 class="text-center">Estás a dos pasos de completar tu orden</h1>

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
              				<caption>
              					<h3>Detalle de tu orden.</h3>
              				</caption>
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
                    				<td colspan="7" class="text-right"><strong>Sub Total: ${{ number_format($total, 2) }}</strong>
                						<br><br>
                						{{ HTML::image('images/mastercardsecurity.png', 'mas', array('width' => 'auto', 'height' => 'auto')) }}
                    					{{ HTML::image('images/verifiedbyvisaecurity.png', 'vis', array('width' => 'auto', 'height' => 'auto')) }}
                					</td>
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
    		<div style="border-bottom: 1px solid #999;">
    			<div class="row">
        		<div class="col-xs-12">
        			<h3>* Búsqueda de Cliente</h3>
      				<div class="form-group">
        				<label>Correo</label>
        				<input type="email" name="find_mail_user" id="find_mail_user" class="form-control" placeholder="Correo de cliente">
        				<br>
          			<label>Teléfono</label>
          			<input type="number" inputmode="numeric" pattern="/^([0-9])*$/" class="form-control" name="find_telefono_user" id="find_telefono_user" placeholder:"Número de cliente">
        			</div>
    			 </div>
      		</div>
    		</div>      		

      		{{ Form::open(['url' => ['order/create'], 'onSubmit'=>'return enviarCompra()','method' => 'POST', 'class' => 'checkout_order', 'id'=>'form']) }} 
        		{{ Form::hidden('nombre_tarjeta', '', array('id' => 'nombre_tarjeta')) }}
        		{{ Form::hidden('tipo_tarjeta', '', array('id' => 'tipo_tarjeta')) }}
        		
        		<h2>1. Tipo de servicio</h2>
        		@foreach($schedule as $key=>$value)
          		@if($value->service_type_id == 1 || $value->service_type_id == 3)
            			@if($value->service_type_id == 3)
              			<input type="hidden" id="serTipe" value="3"/>
            			@else
              			<input type="hidden" id="serTipe" value="2"/>
            			@endif
            			<?php
        					$now = strtotime($date);
  			          	$open = strtotime($value->opening_time);
  			          	$close = strtotime($value->closing_time);

            				if($open<$close){
              				$mostrar = ($now>$open and $close>$now)? true:false;
            				}else{
              				$mostrar = ($now>$open or $close>$now)? true:false;
            				}
            			?>
            			@if($mostrar)
              			<div style="border-bottom: 1px solid #999;" >
              				<div class="radio input-lg">
                				<label for="service_type_1">
    	              			@if($value->service_type_id == 1)
    	                    	{{ Form::radio('service_type_id', '1', true,['id' => 'service_type_1', ]) }}
    	                  	@else
    	                    	{{ Form::radio('service_type_id', '3', true,['id' => 'service_type_1', ]) }}
    	                  	@endif
    	              			A domicilio
                  			</label>
              				</div>
              				<div id="user-address" style="display: none; padding: 0px 1em;">
                				<div class="form-group">
                					@if(is_array($usr_address) && count($usr_address) > 0)
              						  <label for="selType1">Escoge tu dirección de destino:</label>
                						<select name="address_id" id="selType1" class="form-control input-lg" >
                  						
                						</select>
                						<br>
                						<label>Tiempo de entrega estimado</label>
                						<input type="text" id="tiempoEstimado" class="form-control" readonly="readonly" style="background-color:#ffffeb;" >
    							      </div>
							          <div class="form-group">
		                  		<label>O ingresa una nueva dirección:</label>
				                  	<button type="button" class="btn btn-primary active" id="new-address">Ingresa una nueva dirección</button>	
                					@else
                						<h4 class="text-warning">No existe ningún registro de direcciones</h4>
                						<button type="button"  id="new-address">Ingresa una nueva dirección</button>
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
  		        				{{ Form::radio('service_type_id', '2', true,['id' => 'service_type_2']) }}
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

				                $num = idate('H');
				                $min = idate('i');

              				?>
              				<div class="row">
              					<div class="col-xs-6">
                					<select name="hour" id="hour_rest" class="form-control input-lg">
            							@for($i=$oint; $i < 24; $i++)                  									
                							@if(($oint < $cint and ($i>=$num and $i<=$cint))	
            								or $oint > $cint and ($i>=$num or $i<=$cint)) )
                								<option value="{{ $i }}">{{ $i }}</option>
                							@endif
                						@endfor
                					</select>
              					</div>
              					<div class="col-xs-6">
                					<select name="minutes" id="min_rest" class="form-control input-lg">
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
        
        		<div class="row">
          		<div class="col-xs-12">
          			<h3>* Datos Adicionales</h3>
      				  <div class="form-group">
          				<label>Nombre</label>
        				  {{ Form::text('nombre_user', null, array('id'=>'nombre_user', 'placeholder' => 'Nombre','title'=> 'Es obligatorio que ingrese un nombre', 'required', 'class' => 'form-control ')) }}
        				  <br>
            			<label>Teléfono</label>            
            			<input type="number" inputmode="numeric" pattern="/^([0-9])*$/" name="telefono_user" id="telefono_user" placeholder:"Para contactarte si es necesario" title="Es obligatorio que ingrese un número" required="required" class="form-control" onKeyPress="return soloNumeros(event)">
          			</div>
      			 </div>
        		</div>

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
                				<input type="text" class="form-control" aria-describedby="inputGroupSuccess1Status" name="credit_card" placeholder="* Número de tarjeta" id="credit_card" autocomplete="off">
              				</div>
              				<span id="cc_stat_icon" class="fa fa-exclamation fa-lg form-control-feedback" aria-hidden="true" style="top: 10px;"></span>
            			</div>
            			<div id="ccv_grp" class="form-group has-error has-feedback">
              				<input name="secure_code" placeholder="* Código de seguridad" class="form-control" id="inp_ccv" aria-describedby="inputSuccess2Status" type="text" autocomplete="off">
              				<span id="ccv_icon" class="fa fa-exclamation fa-lg form-control-feedback" aria-hidden="true" style="top: 10px;"></span>
            			</div>
            			<div class="form-group">
              				<label>Fecha de vencimiento:</label>
              				<div class="row">
                				<div class="col-xs-6">
				                  	{{ Form::select('month',[
				                  	null => 'Mes',
				                  	1 => '01- Enero',
				                  	2 => '02- Febrero',
				                  	3 => '03- Marzo',
				                  	4 => '04- Abril',
				                  	5 => '05- Mayo',
				                  	6 => '06- Junio',
				                  	7 => '07- Julio',
				                  	8 => '08- Agosto',
				                  	9 => '09- Septiembre',
				                  	10 => '10- Octubre',
				                  	11 => '11- Noviembre',
				                  	12 => '12- Diciembre'], null, array('id'=>'month','class'=>'form-control input-lg')) }}
                				</div>
                				<div class="col-xs-6">
	                				<?php
					                  	$ystart = date('Y');
					                  	$exp_years = array();
					                  	for ($y = $ystart; $y <= ($ystart + 20); $y++) {
					                    	$exp_years[$y] = $y;
					                  	}
					                  	$exp_years = array_add($exp_years, '', 'Año');
					                  	ksort($exp_years);
	            					?>
	                				{{ Form::select('year', $exp_years, null, array('id'=>'year','class'=>'form-control input-lg')) }}
              					</div>
            				</div>
          				</div>
          			</div>
        			</div>

        			<div style="border-bottom: 1px solid #999;">
          			<div class="radio input-lg">
            				<label for="method_3">
              				{{ Form::radio('payment_method_id', '3', null,['id' => 'method_3']) }}
              				Tigo Money
            				</label>
          			</div>
          			<div id="tm" style="display:none; padding: 0px 1em;">
            				<div class="form-group">
              				<label>Número a debitar</label>            
          					{{ Form::text('num_debitar', null, array('id'=>'num_debitar', 'placeholder' => 'Número a debitar', 'class' => 'form-control ')) }}
              				<br>
              				<label>Seleccionar Billetera</label>
  				            {{ Form::select('billetera', [
  				               'Dinero recibido local' => 'Dinero recibido local',
  				               'Mis abonos' => 'Mis abonos',
  				               'Dinero recibido internacionalmente' => 'Dinero recibido internacionalmente']
  				            ) }}
  				            {{ Form::hidden('cargo_uso_tigo', '', array('id' => 'cargo_uso_tigo')) }}
  				            {{ Form::hidden('costo_envio', '', array('id' => 'costo_envio')) }}
            				</div>
          			</div>
        			</div>

  		      	<div style="border-bottom: 1px solid #999;">
          			<div class="radio input-lg">
            				<label for="method_2">
          					{{ Form::radio('payment_method_id', '1', true,['id' => 'method_2']) }}
              				Efectivo
            				</label>
          			</div>
          			<div id="cash" style="display: none; padding: 0px 1em;">
            				<div class="form-group">
              				<p class="help-block">* Recuerde que debe ingresar una cantidad mayor al total de su orden</p>
              				<select id="cash-select" name="cash" class="form-control input-lg"></select>
            				</div>
        					<p id="cambio">Su cambio es de: ${{ number_format(0, 2) }}</p>
          			</div>
          			<div id="fullDetail">
          				<div class="row">
            				<div class="col-xs-8">
              					<label>Sub total: </label>
            				</div>
            				<div class="col-xs-4 text-right">
              					$ <span id="subTotal"></span>
            				</div>
          				</div>

          				<div class="row shipping-charge">
            				<div class="col-xs-8">
              					<label>Cargo por envío: </label>
            				</div>
            				<div class="col-xs-4 text-right">
              					$ <span id="shipping_charge"></span>
            				</div>
          				</div>

          				<div class="row" id="cargo_tigo" style="display:none;">
            				<div class="col-xs-8">
              					<label>Cargo por uso de Tigo Money: </label>
            				</div>
            				<div class="col-xs-4 text-right">
              					$ <span id="tigo_charge"></span>
            				</div>
          				</div>

          				<div class="row" id="cargo_tarjeta" style="display:none;">
            				<div class="col-xs-8">
              					<label>Cargo por uso de tarjeta: </label>
            				</div>
            				<div class="col-xs-4 text-right">
              					$ <span id="card_charge"></span>
            				</div>
          				</div>

          				<div class="row">
            				<div class="col-xs-8">
              					<label>El total a pagar es: </label>
            				</div>
            				<div class="col-xs-4 text-right">
              					$ <span id="total"></span>
            				</div>
          				</div>

      				</div>
        			</div>
      		</div>
      		<div class="text-center" style="padding-top: 1em;">
        			{{ Form::submit('Comprar', array('id'=>'botDesh','class'=>'btn btn-success btn-lg')) }}
      		</div>
  		{{ Form::close() }}
  	</div>
</div>


<div id="confirmacionModal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-sm">
    	<div class="modal-content">
      		<div class="modal-body">
		    	Usuario no encontrado. Agregarlo como nuevo usuario.
		  	</div>
		  	<div class="modal-footer">
		    	<button type="button" data-dismiss="modal" class="btn btn-success" id="btnNewUser">Agregar</button>
		    	<button type="button" data-dismiss="modal" class="btn btn-danger">Cancel</button>
		  	</div>
    	</div>
  	</div>
</div>

<!--Modal para crear nuevo usuario-->
<div class="modal fade" id="newUser">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
        		</button>
        		<h4 class="modal-title">Agregar nuevo usuario</h4>
      		</div>
      		<div class="modal-body">
        		<h3>Nuevo Usuario</h3>
        		<br>
        		<label>Nombres</label>
				<input type="text" name="name_new_user" id="name_new_user" class="form-control" placeholder="Nombres" required>
				<br>
				<label>Apellidos</label>
				<input type="text" name="surname_new_user" id="surname_new_user" class="form-control" placeholder="Apellidos" required>
        		<br>
        		<label>Teléfono</label>
				<input type="text" name="phone_new_user" id="phone_new_user" class="form-control" placeholder="Teléfono" required>
        		<br>
        		<label>Correo</label>
				<input type="email" name="mail_new_user" id="mail_new_user" class="form-control" placeholder="Correo de cliente" required>
				<br>
				<label>Contraseña</label>
				<input type="password" name="pass_new_user" id="pass_new_user" class="form-control" placeholder="Password" required>
      		</div>
      		<div class="modal-footer">        		
        		<button type="button" class="btn btn-success" id="addNewUser">Agregar</button>
        		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      		</div>
		</div><!-- /.modal-content -->
  	</div><!-- /.modal-dialog -->
</div>

<!--Fin modal para crear nuevo usuario-->

<!--Modal para nueva direccion-->
<div class="modal fade gray_content" id="address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">  	
	<div class="modal-dialog">
		<div class="modal-content">
  		<div class="modal-header">
  			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  			<h4>Nueva dirección</h4>
  		</div>
  		<div class="modal-body" >
  			<p>Completa el siguiente formulario para agregar una nueva dirección</p>
  			<p class="bullet">Campos marcados con * son obligatorios</p>
  			<br>
  			<div class="form-group new_add_modal">
    			{{ Form::text('address_name', null, array('id'=>'address_name','placeholder' => '* Nombre de registro ej. Mi casa', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
    			{{ Form::text('address_1', null, array('id'=>'address_1','placeholder' => '* Dirección', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
    			{{ Form::text('reference', null, array('id'=>'reference','placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
    			{{ Form::select('zone_id',$combobox, $selected,array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required'))}}    			
  			</div>
  		</div>
  		<div class="modal-footer" >
    			{{ Form::submit('Agregar', array('id' => 'btnSubmit','class'=>'btn btn-default button_150')) }}
  		</div>
		</div>
	</div>  	
</div>

<!--Fin modal nueva direccion-->

<script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
<script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script>
  	// just for the demos, avoids form submit
  	jQuery.validator.setDefaults({
    	debug: true,
    	success: "valid"
  	});
  	
  	$("#telefono_user").validate(function(event){
    	event.preventDefault();
		rules: {
	      	field: {
	        	required: true
	      	}
    	}
  	});

  	$("#nombre_user").validate(function(event){
    	event.preventDefault();
    	rules: {
      		field: {
    			required: true
      		}
		}
  	});

  	function soloNumeros(e){ 
    	//var key = window.Event ? e.which : e.keyCode 
    	//return ((key >= 48 && key <= 57) || (key==8))

    	k = (document.all) ? e.keyCode : e.which;
	    if (k==8 || k==0) return true;
	    patron = /^([0-9])*$/;
	    n = String.fromCharCode(k);
	    return patron.test(n);
  	}

</script>

<script type="text/javascript">

  /*function procesar(){
    appboy.logCustomEvent("Ir al Carrito");
  }

  function orderAb(){
    appboy.logCustomEvent("Order Abandoned");
  }*/
  
	function valida(e){
  	tecla = (document.all) ? e.keyCode : e.which;

    //Tecla de retroceso para borrar, siempre la permite
    if(tecla==8){
    		return true;
    }
        
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
	}	

	/*$("input:radio[name=location]").click(function(){
  		if($('input:radio[name=location]:checked').val() == 'si'){
	      
  		});
      	if (typeof navigator.geolocation == 'object'){
      		var datos = navigator.geolocation.getCurrentPosition(mostrar_ubicacion);
      	}

      	$("#coordenadas").val(datos);
		}else{
      		$("#coordenadas").val('');
  		}
	});*/

	function sumaCardCharge(){
  		if($("#serTipe").val() == 3){
    		if ($("#service_type_1").prop('checked')) {
    			var tot = $("#subTotal").text();
	      	var shipping_charge = $("#shipping_charge").text();
			    var cargoTarjeta = 0.00;
			    var cargoTigo = 0.00;

    			if ($("#method_1").prop('checked')){
      			cargoTarjeta = 0.04;
      			$("#cargo_tarjeta").show();
      			$("#cargo_tigo").hide();

      			var cargo = (parseFloat(tot) + parseFloat(shipping_charge)) * cargoTarjeta;
      
		        var red = cargo.toString();
		        var valor = red.split('.')
		        var array = new Array();
		        var array2 = new Array();
		        var array3 = new Array();

		        array = valor;
		        array2 = array[1];
		        array3 = array2;

		        var totalMasCargo = (parseFloat(tot) + parseFloat(shipping_charge)) + parseFloat(cargo);
      
		        console.log('valorCob: '+array)
		        console.log('Cobro1: '+shipping_charge);
		        console.log('Cobro2: '+array3[2]);

		        if(array3[2] != 'undefined' && array3[2] <= 5){
	          		var resultRed = (parseFloat(cargo) + 0.01);
		          	var resultTot = (parseFloat(totalMasCargo) + 0.01);
		          	console.log('Resultado Final: '+resultRed.toFixed(2));
		          	console.log('Resultado total: '+resultTot.toFixed(2));
		          	console.log('Resultado sin red: '+totalMasCargo.toFixed(2));
		        }else{
		          	var resultRed = cargo;
		          	var resultTot = totalMasCargo;
		        }

		        $("#card_charge").text(resultRed.toFixed(2));
		        $("#total").text(resultTot.toFixed(2));

    			}else if($("#method_3").prop('checked')){
      			cargoTigo = 0.025;
      			$("#cargo_tigo").show();
      			$("#num_debitar").attr("required", "true");

		        var cargoT = (parseFloat(tot) + parseFloat(shipping_charge)) * cargoTigo;
		        var cargoFinal = (parseFloat(cargoT) + 0);
		        var red = cargoT.toString();
		        var valor = red.split('.')
		        var array = new Array();
		        var array2 = new Array();
		        var array3 = new Array();

		        array = valor;
		        array2 = array[1];
		        array3 = array2;

		        var totalTMasCargo = (parseFloat(tot) + parseFloat(shipping_charge)) + parseFloat(cargoT);
		        console.log('valorCob: '+array)
		        console.log('Cobro1: '+shipping_charge);
		        console.log('Cobro2: '+array3[2]);

      			if(array3[2] != 'undefined' && array3[2] <= 5){
		          	var resultRed = (parseFloat(cargoT) + 0.01);
		          	var resultTot = (parseFloat(totalTMasCargo) + 0.01);
		          	console.log('Resultado Final: '+resultRed.toFixed(2));
		          	console.log('Resultado total: '+resultTot.toFixed(2));
		          	console.log('Resultado sin red: '+totalTMasCargo.toFixed(2));
      			}else{
        				var resultRed = cargoT;
        				var resultTot = totalTMasCargo;
      			}

		        $("#tigo_charge").text(resultRed.toFixed(2));
		        $("#total").text(resultTot.toFixed(2));
		        $("#cargo_uso_tigo").val(cargoT.toFixed(2));
    			}
    		}else if ($("#service_type_2").prop('checked')) {
    			var tot = $("#subTotal").text();
  				//var shipping_charge = $("#shipping_charge").text();
    			var cargoTarjeta = 0.00;
    			var cargoTigo = 0.00;

    			if ($("#method_1").prop('checked')){
      			cargoTarjeta = 0.04;
      			$("#cargo_tarjeta").show();
      			$("#cargo_tigo").hide();

      			var cargo = (parseFloat(tot)) * cargoTarjeta;	        
      			var red = cargo.toString();
		        var valor = red.split('.')
		        var array = new Array();
		        var array2 = new Array();
      			var array3 = new Array();

		        array = valor;
		        array2 = array[1];
		        array3 = array2;

      			var totalMasCargo = (parseFloat(tot)) + parseFloat(cargo);
      
		        console.log('valorCob: '+array)
		        //console.log('Cobro1: '+shipping_charge);
		        console.log('Cobro2: '+array3[2]);

		        if(array3[2] != 'undefined' && array3[2] <= 5){
		          	var resultRed = (parseFloat(cargo) + 0.01);
		          	var resultTot = (parseFloat(totalMasCargo) + 0.01);
		          	console.log('Resultado Final: '+resultRed.toFixed(2));
		          	console.log('Resultado total: '+resultTot.toFixed(2));
		          	console.log('Resultado sin red: '+totalMasCargo.toFixed(2));
		        }else{
		          	var resultRed = cargo;
		          	var resultTot = totalMasCargo;
		        }

		        $("#card_charge").text(resultRed.toFixed(2));
		        $("#total").text(resultTot.toFixed(2));

    			}else if($("#method_3").prop('checked')){
		        cargoTigo = 0.025;
		        $("#cargo_tigo").show();

		        var cargoT = (parseFloat(tot)) * cargoTigo;
		        var cargoFinal = (parseFloat(cargoT) + 0);
		        var red = cargoT.toString();
		        var valor = red.split('.')
		        var array = new Array();
		        var array2 = new Array();
		        var array3 = new Array();

		        array = valor;
		        array2 = array[1];
		        array3 = array2;

		        var totalTMasCargo = (parseFloat(tot)) + parseFloat(cargoT);
		        console.log('valorCob: '+array)
		        //console.log('Cobro1: '+shipping_charge);
		        console.log('Cobro2: '+array3[2]);

		        if(array3[2] != 'undefined' && array3[2] <= 5){
		          var resultRed = (parseFloat(cargoT) + 0.01);
		          var resultTot = (parseFloat(totalTMasCargo) + 0.01);
		          console.log('Resultado Final: '+resultRed.toFixed(2));
		          console.log('Resultado total: '+resultTot.toFixed(2));
		          console.log('Resultado sin red: '+totalTMasCargo.toFixed(2));
		        }else{
		          var resultRed = cargoT;
		          var resultTot = totalTMasCargo;
		        }

		        $("#tigo_charge").text(resultRed.toFixed(2));
		        $("#total").text(resultTot.toFixed(2));
		        $("#cargo_uso_tigo").val(cargoT.toFixed(2));
	      	}
    		}
  		}
	}

	$("#botDesh").click(function(event){
	  	var test = $("#selType1").val();
	  
	  	if(typeof(test) == 'undefined'){
	    	event.preventDefault();
	    	$('#mensaje').modal('show');
	  	}else{
	  	}
	});


	function enviarCompra(){
		//appboy.logCustomEvent("Order Complete");

		document.getElementById("botDesh").value = "Enviando...";
		document.getElementById("botDesh").disabled = true;

		@if(isset($cart) && count($cart) > 0)
  		@foreach($cart as $key=>$product)
    			@if(is_array($product))
      			@foreach($product as $val=>$pro)
        				//appboy.logPurchase("{{ $pro['product_id'] }}", {{ number_format($pro['unit_price'],2) }}, "USD", {{ $pro['quantity'] }});
		        @endforeach
    			@endif
		@endforeach
		@endif

		return true;
	}

	var sub_total = {{ number_format($total, 2) }};
	var shipping_charge = 0.00;


  	function mostrar_ubicacion(p){
	    $("#coordenadas").val(p.coords.latitude+','+p.coords.longitude);
  	}
  
	$(document).ready(function(){

  		$.post('{{URL::to("/getTime")}}',
    		{'direccion':$("#selType1").val()},
    		function(data){
      			console.log(data);
      			if(typeof data !== 'undefined' && data.length > 0){
        			var sep = data[0].prom_time.split(" m");
			        var tiempo = parseInt(sep[0]) + parseInt(20);
			        $("#tiempoEstimado").val(tiempo + ' min');
      			}else{
        			$("#tiempoEstimado").val('Servicio no disponible');
      			}
    		}, 'json'
  		);

  		/*FIN DE TIEMPO ESTIMADO*/

	  	var tiempo = new Date();
	  	var hora = tiempo.getHours();

  		if($("#cont").val() > 0){
    		$("#iconoContador").show();
  		}else{
    		$("#iconoContador").hide();
  		}

  		var sel = $('#zone_id');
  		var opts_list = sel.find('option');
  		opts_list.sort(function(a, b) { return $(a).text() > $(b).text() ? 1 : -1; });
		sel.html('').append(opts_list);

  		$.post('{{URL::to("/getUserData")}}',
			{'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
			function(data){

		        if(data[0].name== null || data[0].last_name== null){
		          	$("#nombre_user").val('');
		          	$("#telefono_user").val('');
		          	if($("#method_3").prop('checked')){
		            	$("#num_debitar").val('');
		          	}
		    	}else{
		          	$("#nombre_user").val(data[0].name+' '+data[0].last_name);
		          	$("#telefono_user").val(data[0].phone);
		      		if($("#method_3").prop('checked')){
		            	$("#num_debitar").val(data[0].phone);
		          	}
		        }
		    }, 'json'
	  	);

	  	if($("#contador_direc").val() == 0 && $("#serTipe").val() >= 2){
	    	$("#service_type_2").removeAttr('checked');
	    	$("#service_type_1").prop( "checked", true );
	  	}else if($("#serTipe").val() < 2){
	    	$("#service_type_2").prop( "checked", true );
	  	}

	    setValues();

	    $("#cash").show();
	    $('.cash_required').attr('required', 'true');
	    $("#credit-card").hide();
	    $(".credit_required").removeAttr('required', 'true');
	    $("#cargo_tarjeta").hide();
	    $("#cargo_tigo").hide();
	    setValues();

    	if($("#service_type_1").prop('checked')){
      		$("#user-address").show();
      		$("#rest-address").hide();
      		$(".shipping-charge").show();
      		console.log("service type 1");
      		if ($("#service_type_1").val() == 3){
        		console.log($("#selType1").val());
          		$.post('{{URL::to("/shipping_charge")}}',
          			{'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
          			function(data){
            			if (data.status){
	              			shipping_charge = data.data.shipping_charge;
	              			$("#costo_envio").val(shipping_charge);
              				console.log(shipping_charge);
              				setValues();
            			} else{
              				alert("Error en el servidor, por favor refrescar");
            			}
          			}, 'json'
        		);

        		$.post('{{URL::to("/getTime")}}',
          			{'direccion':$("#selType1").val()},
          			function(data){
            			console.log(data);
            			if(typeof data !== 'undefined' && data.length > 0){
              				var sep = data[0].prom_time.split(" m");
              				var tiempo = parseInt(sep[0]) + parseInt(20);
              				$("#tiempoEstimado").val(tiempo + ' min');
            			}else{
              				$("#tiempoEstimado").val('Servicio no disponible');
            			}
          			}, 'json'
        		);
      		}else{
	        	shipping_charge = {{$parent_shipping_cost}};
		        setValues();
	        	if($(".shipping-charge").val()==0.0){
		          $(".shipping-charge").hide();
		        }
      		}
    	}

    	if($("#service_type_2").prop('checked')){
      		$("#rest-address").show();
      		$("#user-address").hide();
      		$(".shipping-charge").hide();
      		//$("#cargo_tigo").show();
      		shipping_charge = 0.0;
      		$("#costo_envio").val(shipping_charge);
      		setValues();

      		if($("#method_3").prop('checked')){
        		$("#cargo_tigo").show();
    			$("#cargo_tarjeta").hide();
      		}else if($("#method_1").prop('checked')){
        		$("#cargo_tarjeta").show();
        		$("#cargo_tigo").hide();
      		}
    	}

  		$("#selType1").change(function(){
			if ($("#service_type_1").val() == 3){
	      		$.post('{{URL::to("/shipping_charge")}}',
	        		{'restaurant_id':{{$restaurant_id}}, 'address_id':$(this).val()},
	        		function(data){
		          		if (data.status){
		            		shipping_charge = data.data.shipping_charge;
		            		$("#costo_envio").val(shipping_charge);
		            		setValues();
		          		} else{
		            		alert("Error en el servidor, por favor refrescar");
		          		}
		        	}, 'json'
	      		);

	      		$.post('{{URL::to("/getTime")}}',
	        		{'direccion':$("#selType1").val()},
	        		function(data){
	          			console.log(data);
	          			if(typeof data !== 'undefined' && data.length > 0){
	            			var sep = data[0].prom_time.split(" m");
	            			var tiempo = parseInt(sep[0]) + parseInt(20);
	            			$("#tiempoEstimado").val(tiempo + ' min');
	          			}else{
	            			$("#tiempoEstimado").val('Servicio no disponible');
	          			}
	    			}, 'json'
	      		);
			}else{
	      		shipping_charge = {{$parent_shipping_cost}};
	      		$("#costo_envio").val(shipping_charge);
	      		setValues();
	    	}
	  	});

	  	if($("#method_1").prop('checked')){
	    	$("#credit-card").show();
		    $("#cargoTarjeta").show();
		    $("#cash").hide();
	  	}

  	});

	$("#selType1").change(function(){
	  	if ($("#service_type_1").val() == 3){
	    	$.post('{{URL::to("/shipping_charge")}}',
	      		{'restaurant_id':{{$restaurant_id}}, 'address_id':$(this).val()},
	      		function(data){
	    			if (data.status){
	          			shipping_charge = data.data.shipping_charge;
	          			$("#costo_envio").val(shipping_charge);
	          			setValues();
	        		} else{
	          			alert("Error en el servidor, por favor refrescar");
	        		}
	      		}, 'json'
	    	);

			$.post('{{URL::to("/getTime")}}',
	      		{'direccion':$("#selType1").val()},
	      		function(data){
	        		console.log(data);
	    			if(typeof data !== 'undefined' && data.length > 0){
	          			var sep = data[0].prom_time.split(" m");
	          			var tiempo = parseInt(sep[0]) + parseInt(20);
	          			$("#tiempoEstimado").val(tiempo + ' min');
	        		}else{
	          			$("#tiempoEstimado").val('Servicio no disponible');
	        		}
	      		}, 'json'
	    	);
	  	}else{
		    shipping_charge = {{$parent_shipping_cost}};
		    $("#costo_envio").val(shipping_charge);
		    setValues();
		    $(".shipping-charge").hide();
	  	}
	});

	function setValues(){
  		var total = parseFloat(sub_total) + parseFloat(shipping_charge);
		$("#subTotal").text(sub_total.toFixed(2));
  		$("#shipping_charge").text(parseFloat(Math.round(shipping_charge * 100) / 100).toFixed(2));
  		$("#total").text(total.toFixed(2));
		$("#costo_envio").val(parseFloat(Math.round(shipping_charge * 100) / 100).toFixed(2));
		sumaCardCharge();
  		$("#cash-select").html('');
  		var v = Math.ceil(total);
  		var five = 0;
  		var ten = 0;
  		var tweny = 0;

  		$("#cash-select").append($("<option/>", {'val':total, 'text':"$"+total.toFixed(2)}));

	  	//Imprimir el redondeado
		if(v!=total)
			$("#cash-select").append($("<option/>", {'val':v, 'text':"$"+v.toFixed(2)}));

  		//Incrementar si redondeado es multiplo de 5
	  	if(v%5==0){
		    v++;
	  	}

  		for(var i = v; i <=v+20; i++){
    		if(five==0 && ten==0 && tweny==0 && i%5==0){
      			//Es múltiplo de 5
      			five=i;
      			$("#cash-select").append($("<option/>", {'val':i, 'text':"$"+i.toFixed(2)}));
    		}

    		if(ten==0 && tweny==0 && i%10==0){
      			//Es múltiplo de 10
      			ten = i;

      			if(five!=i)
      				$("#cash-select").append($("<option/>", {'val':i, 'text':"$"+i.toFixed(2)}));
    		}

    		if(tweny==0 && i%20==0){
      			//Es múltiplo de 20
      			tweny = i;
      			if(five!=i && ten!=i)
      				$("#cash-select").append($("<option/>", {'val':i, 'text':"$"+i.toFixed(2)}));
    		}
  		}

  		var str = "";
  		var s = $("#cash-select").val();
		var t = parseFloat(sub_total)+parseFloat(shipping_charge);
  		if (s > 0){
    		var nt = s - t;
    		str += "Su cambio será de: $ <strong>" + nt.toFixed(2) + "</strong>";
  		} else{
    		str += "Su cambio será de: <strong>$ 0.00</strong>";
  		}

  		$("#cambio").html(str);
	}

	$("#service_type_1").click(function(){
  		$("#user-address").show();
  		$("#rest-address").hide();
  		$(".shipping-charge").show();
  		console.log("service type 1");
  		if ($("#service_type_1").val() == 3){
    		console.log($("#selType1").val());
    		$.post('{{URL::to("/shipping_charge")}}',
      			{'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
      			function(data){
        			if (data.status){
          				shipping_charge = data.data.shipping_charge;
          				console.log(shipping_charge);
          				setValues();
        			} else{
          				alert("Error en el servidor, por favor refrescar");
        			}
      			}, 'json'
    		);

	    	$.post('{{URL::to("/getTime")}}',
	      		{'direccion':$("#selType1").val()},
	      		function(data){
	        		console.log(data);
	        		if(typeof data !== 'undefined' && data.length > 0){
	          			var sep = data[0].prom_time.split(" m");
	          			var tiempo = parseInt(sep[0]) + parseInt(20);
	          			$("#tiempoEstimado").val(tiempo + ' min');
	        		}else{
	          			$("#tiempoEstimado").val('Servicio no disponible');
	        		}
	      		}, 'json'
	    	);
	  	} else{
    		shipping_charge = {{$parent_shipping_cost}};
    		setValues();
    		if($(".shipping-charge").val()==0.0){
      			$(".shipping-charge").hide();
    		}
  		}
	});

	$("#service_type_2").click(function(){
  		$("#rest-address").show();
  		$("#user-address").hide();
  		$(".shipping-charge").hide();
  		shipping_charge = 0.0;
  		setValues();

  		if($("#method_3").prop('checked')){
    		$("#cargo_tigo").show();
    		$("#cargo_tarjeta").hide();
  		}else if($("#method_1").prop('checked')){
    		$("#cargo_tarjeta").show();
    		$("#cargo_tigo").hide();
  		}
	});

	$("#method_1").click(function(){
  		$("#credit-card").show();
  		$('.credit_required').attr('required', 'true');
  		$("#cash").hide();
  		$(".cash_required").removeAttr('required', 'true');
  		$("#tm").hide();
  		setValues();
	});

	$("#method_2").click(function(){
  		$("#cash").show();
  		$('.cash_required').attr('required', 'true');
  		$("#credit-card").hide();
  		$(".credit_required").removeAttr('required', 'true');
  		$("#cargo_tarjeta").hide();
  		$("#tm").hide();
  		$("#cargo_tigo").hide();
  		setValues();
	});

	$("#method_3").click(function(){
  		$("#tm").show();
  		$("#cargo_tigo").show();
  		$('.cash_required').attr('required', 'true');
  		$("#credit-card").hide();
  		$(".credit_required").removeAttr('required', 'true');
  		$("#cargo_tarjeta").hide();
  		$("#cash").hide();
  		setValues();
	});

	$("#cash-select").change(function() {
  		var str = "";
  		var s = $(this).val();
  		var t = parseFloat(sub_total)+parseFloat(shipping_charge);
  		if (s > 0){
    		var nt = s - t;
    		str += "Su cambio será de: $ <strong>" + nt.toFixed(2) + "</strong>";
  		} else{
    		str += "Su cambio será de: <strong>$ 0.00</strong>";
  		}

  		$("#cambio").html(str);
	});

	//se valida el codigo de seguridad para los tipos de tarjetas de credito
	var ccv_n = 0;
	$(function() {
  		$('#credit_card').validateCreditCard(function(result) {
    		$('#showmsg').html('Card type: ' + (result.card_type == null ? '-' : result.card_type.name)
    		+ '<br>Valid: ' + result.valid
    		+ '<br>Length valid: ' + result.length_valid
    		+ '<br>Luhn valid: ' + result.luhn_valid);
    		if (result.card_type != null){
      			$('#inp_grp_add').addClass(result.card_type.name);
      			$("#tipo_tarjeta").val(result.card_type.name);
      			if(result.card_type.name == 'amex'){
        			ccv_n = 4
        			$("#nombre_tarjeta").val(ccv_n);
        			$('#inp_ccv').attr("minlength", ccv_n);
        			$('#inp_ccv').attr("maxlength", ccv_n);
      			}else{
        			ccv_n = 3
        			$("#nombre_tarjeta").val(ccv_n);
        			$('#inp_ccv').attr("maxlength", ccv_n);
        			$('#inp_ccv').attr("minlength", ccv_n);
      			}
      
      			if (result.valid == true){
        			$('#cc_frm_grp').removeClass('has-error');
			        $('#cc_frm_grp').addClass('has-success');
			        $('#cc_stat_icon').removeClass('fa-exclamation');
			        $('#cc_stat_icon').addClass('fa-check');
      			} else{
			        $('#cc_frm_grp').removeClass('has-success');
			        $('#cc_frm_grp').addClass('has-error');
			        $('#cc_stat_icon').addClass('fa-exclamation');
			        $('#cc_stat_icon').removeClass('fa-check');
      			}
    		} else{
      			$('#inp_grp_add').removeClass();
  				$('#inp_grp_add').addClass('input-group-addon');
  				$('#inp_ccv').attr("maxlength", - 1);
    		}
  		}, { accept: ['visa', 'mastercard', 'amex'] });
	});
	
	$("#inp_ccv").keyup(function() {
  		this.value = this.value.replace(/[^0-9\.]/g, '');
  		var ln = $(this).val().length;
		if (ln == ccv_n){
    		$('#ccv_grp').removeClass('has-error');
    		$('#ccv_grp').addClass('has-success');
    		$('#ccv_icon').removeClass('fa-exclamation');
    		$('#ccv_icon').addClass('fa-check');
  		} else{
		    $('#ccv_grp').removeClass('has-success');
		    $('#ccv_grp').addClass('has-error');
		    $('#ccv_icon').removeClass('fa-check');
		    $('#ccv_icon').addClass('fa-exclamation');
  		}
	});
</script>


<!-- for new addresses -->
<script>
	var municipality = '';
	var zone = '';
	var idEdit = 0;

	$(document).ready(function(){
  		$("#state").change(function(){
			getMunicipalities($(this).val());
  		});

  		$("#municipality").change(function(){
    		getZones($(this).val());
  		});

  		$('#address').on('hidden.bs.modal', function () {
    		idEdit = 0;
    		$("#address-form")[0].reset();
  		});
	});

	function enviar(){
  		var params = $("#address-form").serialize();
		if(idEdit!=0){
    		params+='&address_id='+idEdit;
  		}
  		
  		$.post((idEdit!=0)? '{{URL::to("/address/edit")}}':'{{URL::to("/address/create")}}',
		params,
		function(data){
			if(data.status){
				$("#address").modal('hide');
				location.reload();
			}else{
				var s;
				$.each(data.data, function(i, item){
				s = item;
				});

				alert(s);
			}
		}, 'json');

		return false;
	}

	function getMunicipalities(idState){
  		$.post('{{URL::to("user/address/municipalities")}}'
  			,{'state_id':idState},
  			function(data){
    			if(data.status){
      				$("#municipality").html('');
      				$("#municipality").append($("<option/>", {'value':'', 'text':'--Seleccione un municipio--'}));
      				$.each(data.data, function(i, item){
        				$("#municipality").append($("<option/>", {'value':item.municipality_id, 'text':item.municipality}));
      				});
      				$("#municipality").val(municipality);
      				municipality='';

      				if(idEdit==0){
        				$("#zone_id").html('');
				        $("#zone_id").append($("<option/>", {'value':'', 'text':'--Seleccione una zona--'}));
				        $("#zone_id").val('');
      				}
    			}else{
      				alert("Ocurrió un error al obtener los municipios del departamento");
    			}
  			}, 'json'
		);
	}

	function getZones(idMunicipality){
  		$.post('{{URL::to("user/address/zonesByMunicipality")}}'
  			,{'municipality_id':idMunicipality},
  			function(data){
    			if(data.status){
	      			$("#zone_id").html('');

	      			$("#zone_id").append($("<option/>", {'value':'', 'text':'--Seleccione una zona--'}));

	      			$.each(data.data, function(i, item){
	        			$("#zone_id").append($("<option/>", {'value':item.zone_id, 'text':item.zone}));
	      			});

	  				$("#zone_id").val(zone);
	      			zone = '';
    			}else{
      				alert("Ocurrió un error al obtener las zonas del municipio");
    			}
  			}, 'json'
		);
	}

	function edit(idAddress){
  		idEdit=idAddress;
  		$.get("{{URL::to('user/address/edit/')}}/"+idAddress,
		function(data){
			$("#address_name").val(data.address.address_name);
			$("#address_1").val(data.address.address_1);
		    $("#address_2").val(data.address.address_2);
		    $("#reference").val(data.address.reference);
		    $("#state").val(data.municipality.state_id);

		    municipality = data.municipality.municipality_id;
			zone = data.zone.zone_id;

			getMunicipalities(data.municipality.state_id);
			getZones(data.municipality.municipality_id);
		}, 'json');
	}

	/******MANDAR A BUSCAR LA INFORMACION DEL CLIENTE POR EMAIL*****/
	$('#find_mail_user').bind("enterKey",function(e){
		//alert("Enter");
		var mail_user = $("#find_mail_user").val();
		//var url= "http://localhost/pf-callcenter/public/getDataOrder";
		var url="http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/getDataOrder";
		$.ajax({
	        type: 'POST',
	        url: url,
	        dataType: 'json',
	        data: {
	          type : 'email',
	          mail : mail_user
	        }
	    })
	    .done(function (result){
        console.log(result['result']);

        if(result['result'] != 0){
        	$.each(result['direcciones'], function( index, value ) {
			  		$("#selType1").append('<option value="'+index+'">' + value + '</option>');
  				});

  				$.each(result['result'], function( index, value ) {
            if(value['nombre_cliente_orden'] == ''){
              $("#nombre_user").val(value['nombres']+' '+value['apellidos']);
            }else{
              $("#nombre_user").val(value['nombre_cliente_orden']);
            }
            
            if(value['telefono_cliente_orden'] == ''){
              $("#telefono_user").val(value['telefono_cliente']);
            }else{
              $("#telefono_user").val(value['telefono_cliente_orden']);
            }				  	
  				});

  				//Mando a traer el costo de envío al cargar las direcciones
  				$.post('{{URL::to("/shipping_charge")}}',
      			{'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
      			function(data){
        			if (data.status){
              			shipping_charge = data.data.shipping_charge;
              			$("#costo_envio").val(shipping_charge);
          				console.log(shipping_charge);
          				setValues();
        			} else{
          				alert("Error en el servidor, por favor refrescar");
        			}
      			}, 'json'
	    		);
        }else if(typeof result['query'] !== 'undefined'){
          console.log('ASI FUNCIONAAAAA');          
          console.log(result);
          $.each(result['query'], function( index, value ) {
            $.each(result['query'], function( index, value ) {
              /*$("#nombre_user").val(value['nombre_cliente_orden']);
              $("#telefono_user").val(value['telefono_cliente_orden']);*/
              $("#nombre_user").val(value['nombres']+' '+value['apellidos']);
              $("#telefono_user").val(value['telefono']);
            });
          });

          if(result['direcciones'] != 0){
            console.log('lleva direcciones');
            $.each(result['direcciones'], function( index, value ) {
              $("#selType1").append('<option value="'+index+'">' + value + '</option>');
            });
          }
        }else{
        	$("#confirmacionModal").modal('show');	        	
        }
	    })
	    .fail(function (result){
	        alert("No se pudo realizar la acción, por favor intenta de nuevo");
	    });

	});
	
	$('#find_mail_user').keyup(function(e){
		if(e.keyCode == 13){
		  $(this).trigger("enterKey")
		}
	});

	/******MANDAR A BUSCAR LA INFORMACION DEL CLIENTE POR TELEFONO*****/
	$('#find_telefono_user').bind("enterKey",function(e){
		//alert("Enter");
		var number_user = $("#find_telefono_user").val();
		//var url= "http://localhost/pf-callcenter/public/getDataOrder";
		var url = "http://ec2-54-236-21-142.compute-1.amazonaws.com/pidafacil-callcenter/public/getDataOrder";

		$.ajax({
	        type: 'POST',
	        url: url,
	        dataType: 'json',
	        data: {
	          type : 'telefono',
	          number : number_user
	        }
	    })
	    .done(function (result){
        console.log(result['result']);

  			if(result['result'] != 0){
          $.each(result['direcciones'], function( index, value ) {
            $("#selType1").append('<option value="'+index+'">' + value + '</option>');
          });

          $.each(result['result'], function( index, value ) {
            if(value['nombre_cliente_orden'] == ''){
              $("#nombre_user").val(value['nombres']+' '+value['apellidos']);
            }else{
              $("#nombre_user").val(value['nombre_cliente_orden']);
            }
            
            if(value['telefono_cliente_orden'] == ''){
              $("#telefono_user").val(value['telefono_cliente']);
            }else{
              $("#telefono_user").val(value['telefono_cliente_orden']);
            }           
          });

          //Mando a traer el costo de envío al cargar las direcciones
          $.post('{{URL::to("/shipping_charge")}}',
            {'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
            function(data){
              if (data.status){
                    shipping_charge = data.data.shipping_charge;
                    $("#costo_envio").val(shipping_charge);
                  console.log(shipping_charge);
                  setValues();
              } else{
                  alert("Error en el servidor, por favor refrescar");
              }
            }, 'json'
          );
        }else if(typeof result['query'] !== 'undefined'){
          console.log('ASI FUNCIONAAAAA');          
          console.log(result);
          $.each(result['query'], function( index, value ) {
            $.each(result['query'], function( index, value ) {
              /*$("#nombre_user").val(value['nombre_cliente_orden']);
              $("#telefono_user").val(value['telefono_cliente_orden']);*/
              $("#nombre_user").val(value['nombres']+' '+value['apellidos']);
              $("#telefono_user").val(value['telefono']);
            });
          });

          if(result['direcciones'] != 0){
            console.log('lleva direcciones');
            $.each(result['direcciones'], function( index, value ) {
              $("#selType1").append('<option value="'+index+'">' + value + '</option>');
            });
          }
        }else{
          $("#confirmacionModal").modal('show');            
        }
      })
	    .fail(function (result){
	        alert("No se pudo realizar la acción, por favor intenta de nuevo");
	    });
	});
	
	$('#find_telefono_user').keyup(function(e){
		if(e.keyCode == 13){
		  $(this).trigger("enterKey")
		}
	});

	/**********SCRIPTS PARA REGISTRAR UN NUEVO USUARIO**********/
	$("#btnNewUser").click(function(){
		$("#confirmacionModal").modal('hide');
		$("#newUser").modal('show');
	});

	$("#addNewUser").click(function(){
		var name_user = $("#name_new_user").val();
		var lastname_user = $("#surname_new_user").val();
		var phone_user = $("#phone_new_user").val();
		var mail_user = $("#mail_new_user").val();
		var pass_user = $("#pass_new_user").val();    

		if(mail_user == ''){
			alert("¡Debes ingresar un correo electrónico!");
		}else if(pass_user == ''){
			alert("¡Debes ingresar una contraseña!");
		}else if(mail_user != '' && pass_user != ''){
			$.post('{{URL::to("/register")}}',
  			{
  				'name': name_user,
  				'last_name': lastname_user,
  				'phone': phone_user,
  				'email':mail_user,
  				'password':pass_user
  			},
  			function(data){
  				console.log(data);          
  				if(data != 0){
  					alert("Usuario registrado");            
  					$("#nombre_user").val(data.name+' '+data.last_name);
		  		  $("#telefono_user").val(data.phone);
		  		  localStorage.setItem("user_id", data.user_id);
            $("#newUser").modal('hide');
            location.reload();
  				}else{
  					alert("Error!");
  				}
  			}, 'json'
  		);
		}
	});

	$("#new-address").click(function(){
		$("#address").modal('show');
		//$("#user_id_adress").val(localStorage.getItem("user_id"));
	});

  $("#btnSubmit").click(function(event){

    var address_name = $("#address_name").val();
    var address_1 = $("#address_1").val();
    var reference = $("#reference").val();
    var zone_id = $("#zone_id option:selected" ).val();
    var user_id_adress = localStorage.getItem("user_id");

    if($("#zone_id option:selected" ).val()== 'none'){
      event.preventDefault();
      alert("Debes elegir una zona.")
    }else{      
    }

    if(address_name == '' || address_1 == '' || reference == '' || $("#zone_id option:selected" ).val()== 'none'){
      alert("Faltan campos que rellenar");
    }else{
      $.post('{{URL::to("/address/create")}}',
        {
          'user_id_adress': user_id_adress,
          'address_name': address_name,
          'address_1': address_1,
          'reference': reference,
          'zone_id': zone_id
        },
        function(data){
          console.log(data);
          if(data['direcciones'] != 0){
            $("#address").modal('hide');
            $.each(data['direcciones'], function(index, value) {
              $("#selType1").append('<option value="'+index+'">' + value + '</option>');
            });

            //Mando a traer el costo de envío al cargar las direcciones
            $.post('{{URL::to("/shipping_charge")}}',
            {'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
              function(data){
                if (data.status){
                  shipping_charge = data.data.shipping_charge;
                  $("#costo_envio").val(shipping_charge);
                  console.log(shipping_charge);
                  setValues();
                } else{
                    alert("Error en el servidor, por favor refrescar");
                }
              }, 'json'
            );
            
          }else{
            alert("Error!");
          }
        }, 'json'
      );
    }
  });


</script>

@stop