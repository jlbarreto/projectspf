@extends('general.visor_template')
@section('content')
<style type="text/css">
	th{
		color:black;
	}

	#tabla{
		margin-left: 25px;
	}

	*{
		color: black;
	}

</style>
	
	<div class="table-responsive" id="tabla">
		<button class="btn btn-success" id="busquedaOrd" style="float:right;">Buscar</button>
		<h2 style="color:black; text-align:center; margin:0px auto;">Pago a Motociclistas</h2>
		<hr />
		<table class="table">
			<thead>
				<th>Orden</th>
				<th>Fecha</th>
				<th>Pago</th>
				<th>Dirección</th>
				<th>Zona</th>
				<th>Costo Envío</th>
				<th>Pago a Motociclista</th>
				<th>Motorista</th>
				<th>Estado</th>
				<th>Detalle</th>
			</thead>
			<tbody>
				@if(isset($orders))
	              	@foreach($orders as $key=>$order)
	              		<?php

	              			$estado_ord = DB::select('
								SELECT os.order_status_id
								FROM req_order_status_logs as os INNER JOIN req_orders as ro ON os.order_id= ro.order_id WHERE os.order_id = ? order by os.created_at DESC limit 1' , array($order->order_id));
							
							$comentario = DB::select('
								SELECT roc.order_id, roc.motivo, roc.comment, roc.user_name
								FROM req_orders_comment as roc 
								INNER JOIN req_orders as ro ON roc.order_id= ro.order_id 
								WHERE roc.order_id = ? ' , array($order->order_id));
	              		?>

		              	<tr id="col_{{$order->order_id}}">
		              		<?php
		              			$date2 = date("Y-m-d H:i:s");
		              			$date3 = $order->created_at;
								$datetime2 = new DateTime($date2);
								$datetime1 = new DateTime($date3);

		              			$date3 = $order->created_at;
								$dteDiff  = $datetime2->diff($datetime1); 
								$horaF = $dteDiff->format("%H:%I:%S");

								$horaComplete = DB::select('
							      Select ords.created_at from pf.req_order_status_logs as ords
							      where ords.order_id = "'.$order->order_id.'" AND ords.order_status_id = 5
							    ');
							    $arrayComplete = json_decode(json_encode($horaComplete), true);
							      	foreach ($arrayComplete as $key => $value) {
							        	$completo = $value['created_at'];
							        	$dateCo = new DateTime($completo);
							        	$horaFinalCo = $dateCo->diff($datetime1);
							        	$horaFinalCo2 = $horaFinalCo->format("%H:%I:%S");
							      	}
		              		?>
		              		@if($estado_ord[0]->order_status_id < 5)
		              			<td>
								    <strong>{{ $order->order_cod }}</strong>
								    <br>
								    <p class="timercont" data-timer="{{$horaF}}" data-idorden="{{ $order->order_id }}"></p>
								   		
								   	<input type="hidden" value="restaurant-orders/time" id="urltime_{{$order->order_id}}" />
							      	<input type="hidden" id="idOrden" value="{{$order->order_id}}"/>
							    </td>
		              		@else
		              			<td>
								    <strong>{{ $order->order_cod }}</strong>
								    <br>
								    @if(isset($horaFinalCo2))
								    	<p class="fa fa-clock-o" aria-hidden="true" style="color:#70bccc;">{{ $horaFinalCo2 }}</p>
								   	@else
								   	@endif
								</td>
		              		@endif
		              		<td>{{$order->created_at}}</td>
		      				<td>
		      				@if($order->payment_method_id == 1)
		      					<i class="fa fa-money fa-2x" style="color:green;"></i>
		      				@elseif($order->payment_method_id == 3)
        						<i>{{ HTML::image('images/tm.png', '', array('style' => 'width:62px; margin-left: -20px; margin-top:-8px;')) }}</i>
		      				@elseif($order->payment_method_id == 2)
		      					<i class="fa fa-credit-card fa-2x" style="color:#0040FF;"></i>
		      				@endif
		      				</td>
		              		<td>{{$order->address}}</td>
		              		<?php 
						      	$direccion = $order->address_id;
						      	$nombre_zona = DB::select('
						        	Select zon.zone from pf.diner_addresses as direc
						        	inner join pf.com_zones as zon ON direc.zone_id = zon.zone_id
						        	where direc.address_id = "'.$direccion.'"
						      	');
						      	$array = json_decode(json_encode($nombre_zona), true);
						      	foreach ($array as $key => $value){
						        	$zonaF = $value['zone'];
						      	}
						    ?>
						    @if(isset($zonaF) && $zonaF !='' and $order->service_type_id==3)
						      	<td>{{$zonaF}}</td>
						    @else
						      	<td>Sin Zona</td>
						    @endif
						    <td>$ {{ $order->shipping_charge }}</td>
						    <?php
						    	$montoT = round($order->shipping_charge * 0.70, 2);
						    	$montoF = number_format($montoT, 2, '.', '');
						    ?>
						    <td>$ {{ $montoF }}</td>
						    <?php
						        $nombre_mt = $order->order_id;
						        $motorista_name = DB::select('select mo.nombre FROM pf.motoristas as mo inner join pf.mensajero as rm
						          	ON mo.motorista_id = rm.motorista_id where rm.order_id = "'.$nombre_mt.'" 
						        ');
						        $nombre_moto = json_decode(json_encode($motorista_name), true);
						          	foreach ($nombre_moto as $key => $value){
						            	$nombreMot = $value['nombre'];
						          	}

						        if(Restaurant::where('parent_restaurant_id', $order->restaurant_id)->get()) {
						          	$sucursales = Restaurant::where('parent_restaurant_id', $order->restaurant_id)->get();
						        }
						    ?>
						    @if(isset($nombreMot))
					        	<td>{{$nombreMot}}</td>
					        @else
					        	<td>No Asignado</td>
					        @endif
					        <?php
					        	$estado_ord = DB::select('
									SELECT os.order_status_id
									FROM req_order_status_logs as os INNER JOIN req_orders as ro ON os.order_id= ro.order_id WHERE os.order_id = ? order by os.created_at DESC limit 1' , array($order->order_id));
					        ?>
					        @if($estado_ord[0]->order_status_id < 4)
					        	<td>Por Recoger</td>
					        @elseif($estado_ord[0]->order_status_id == 4)
					        	<td>Despachada</td>
					        @elseif($estado_ord[0]->order_status_id == 5)
					        	<td>Entregada</td>
					        @elseif($estado_ord[0]->order_status_id > 5 && $estado_ord[0]->order_status_id <= 9)
					        	<td>Cancelada</td>
					        @endif
		              		<td>
						    	<button id="order_id_{{ $order->order_id }}" class="btn btn_edit button_100 mostrarDetalle">Ver detalle</button>
						    </td>
		              	</tr>
						
		              	<div class="modal fade" id="order_detail_{{$order->order_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						    <div class="modal-dialog modal-lg">
						        <div class="modal-content">
						          	<div  class="modal-header">
							            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							            <h4 class="modal-title" id="myModalLabel"><strong>Código de Orden: {{ $order->order_cod }}</strong></h4>
						          	</div>
						          	<div class="modal-body">
						            	<div class="row">
							              	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
								                @if ($order->customer == NULL)
								                  	@if(isset($users->name) && isset($users->last_name))
								                  		<h1>{{ $users->name . ' ' . $users->last_name }}</h1>
								                  	@else
								                  		<h1>Usuario Pidafacil</h1>
								                  	@endif
								                @else
								                  	<h1>{{ isset($order->customer) ? Str::length($order->customer) > 2 ? $order->customer : $users->name . ' ' . $users->last_name : $order->customer }}</h1>
								                @endif
								                <?php 
								                  	$direccion = $order->address_id;
								                  	$nombre_zona = DB::select('
								                    	Select zon.zone from pf.diner_addresses as direc
								                    	inner join pf.com_zones as zon ON direc.zone_id = zon.zone_id
								                    	where direc.address_id = "'.$direccion.'"
								                  	');
								                  	$array = json_decode(json_encode($nombre_zona), true);
								                  	foreach ($array as $key => $value) {
								                    	$zonaF2 = $value['zone'];
								                  	}
								                ?>
								                <?php 
								                  	$direccion = $order->address_id;
								                  	$referencia = DB::select('
								                    	Select ref.reference from pf.diner_addresses as ref
								                    	inner join pf.req_orders as do ON ref.address_id = do.address_id
								                    	where ref.address_id = "'.$direccion.'"
								                  	');
								                  	$array = json_decode(json_encode($referencia), true);
								                  	foreach ($array as $key => $value){
								                    	$ref = $value['reference'];
								                  	}
								                ?>
								                <p>{{ $order->address }}</p>

								                @if(isset($ref) && $ref !='')
								                  	<p>Referencia: {{$ref}}</p>
								                @else
								                  	<p>Referencia:</p>
								                @endif

								                @if(isset($zonaF2) && $zonaF2 !='')
								                  	<p>Zona: {{$zonaF2}}</p>
								                @else
								                  	<p>Zona:</p>
								                @endif

								                @if(isset($_GET['fillter']) && $_GET['fillter']<= 4)
								                  	@if ($order->customer_phone == NULL)
								                    	<p>{{ $users->phone }} | {{ $users->email }}</p>
								                  	@else
								                    	<p>{{ $order->customer_phone }} | {{ $users->email }}</p>
								                  	@endif
								                @else
								                @endif
								                
								                <p><strong>Fecha:</strong> {{ date('d/m/y g:i a', strtotime($order->created_at)) }}</p>
							              	</div>
							              	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
							               		<h1 style="width:100%;">$ {{ $order->order_total }}</h1>
							              	</div>
						            	</div>

						            	<div class="row" >
						            		<div class="col-md-12">
						            			<h3>Comentarios sobre pedido</h3>
						            		</div>
						            	</div>
						            	<div class="row" style="border-top:solid 1px #cccccc;">
						            		<div class="col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						            			Motivo
						            		</div>
						            		<div class="col-md-5" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						            			Comentario
						            		</div>
						            		<div class="col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						            			Usuario
						            		</div>
						            	</div>
						            	@if(isset($comentario))
							            	@foreach($comentario as $key => $value)
							            		<div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
							            			<div class="col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
							            				{{$value->motivo}}
							            			</div>
							            			<div class="col-md-5" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
							            				{{$value->comment}}
							            			</div>
							            			<div class="col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
							            				{{$value->user_name}}
							            			</div>
							            		</div>
							            	@endforeach
						            	@endif
						              	<h3>Datos de pago</h3>
						              	<div class="row" style="border-top:solid 1px #cccccc;">
						                	<div class="col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  		Restaurante
					                		</div>
						                	<div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  		Costo de envío
						                	</div>
						                	<div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  		Total Cliente
						                	</div>
						                	<div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  		Cambio
						                	</div>
						              	</div>
						              	<?php 
						              		$comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2); ?>
						              	<div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
						                	<div class="col-md-3" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
						                  		$ {{ number_format($order->order_total - $comision,2) }}
						                	</div>
						                	
						                	<div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
						                  		$ {{ number_format($order->shipping_charge,2) }}
						                	</div>
						                	<div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
						                  		$ {{ number_format($order->order_total + $order->shipping_charge,2) }}
						                	</div>
						                	<div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
						                  		$ {{ number_format($order->pay_change,2) }}
						                	</div>
						              	</div>
						          	</div>
							        <div class="modal-footer">
							        	<button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
							        </div>
						        </div>
					      	</div>
					    </div>
	              	@endforeach
	            @endif
			</tbody>
		</table>
	</div>

	<div id="myModal" class="modal fade" role="dialog">
	  	<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
	        		<h4 class="modal-title">Realizar Búsqueda</h4>
	      		</div>
	      		<div class="modal-body">
	        		<div class="col-md-3">
		        		<select name="moto_id" class="motorista form-control" id="moto_id" style="width: auto;">
		                  <option value="0">Elegir Motociclista</option>
		                  @foreach($motoristas as $k => $motorista)
		                    <option value="{{$motorista->motorista_id}}">{{$motorista->nombre}}</option>
		                  @endforeach
		                </select>
	                </div>
					<div id="mostrar_fechas" style="display:block;">
						<div class="col-md-3">
							<input type="text" id="datepicker1" class="form-control" placeholder="Fecha Inicio">
						</div>
						<div class="col-md-3">							
							<input type="text" id="datepicker2" class="form-control" placeholder="Fecha Fin">
						</div>
					</div>
					<button id="buscar" class="btn btn-info">Buscar</button>
					<button id="limpiar" class="btn btn-danger">Limpiar</button>
	      		</div>
	      		<hr/>
	      		<div class="table-responsive">
	      			<table class="table" id="tablaBusqueda">
	      				<tr>
	      					<th>Orden</th>
							<th>Fecha</th>
							<th>Pago</th>
							<th>Dirección</th>
							<th>Zona</th>
							<th>Costo Envío</th>
							<th>Pago a Motociclista</th>
							<th>Motorista</th>
	      				</tr>
	      				<tr id="fila"></tr>
	      			</table>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	    	</div>
	  	</div>
	</div>

	{{HTML::style('css/jquery.datetimepicker.css')}}
    {{ HTML::script('js/jquery.datetimepicker.js', array("type" => "text/javascript")) }}
	<script type="text/javascript">

		/*MOSTRAR MODAL*/
	    $(".mostrarDetalle").on('click', function(){
	        var $btn = $(this);
	        var id_elemento = $(this).attr('id');
	        var id_pedido = id_elemento.split("_");
	        
	        $("#order_detail_"+id_pedido[2]).modal("show");
	    });

	    $("#busquedaOrd").click(function(){
	    	$("#myModal").modal('show');
	    });

	    $("#motivo_busqueda").change(function(){
            var valor = $('#motivo_busqueda').val();

            if(valor == "fecha"){
                $("#mostrar_fechas").show();
                $("#moto_list").hide();
                $("#pago_list").hide();
            }else if(valor == "moto"){
                $("#mostrar_fechas").hide();
                $("#moto_list").show();
                $("#pago_list").hide();
            }else if(valor == "pago"){
                $("#pago_list").show();
                $("#mostrar_fechas").hide();
                $("#moto_list").hide();
            }
        });

        $(document).ready(function() {
            $("#datepicker1").datetimepicker({
                format:'Y-m-d H:i:s',
                lang:'es'
            });

            $("#datepicker2").datetimepicker({
                format:'Y-m-d H:i:s',
                lang:'es'
            });
        });

        /*Busqueda de ordenes pasadas*/
        $("#buscar").click(function(){
        	
        	//VALIDAR SI LA TABLA YA TIENE REGISTROS*************

        	var motociclista = $("#moto_id").val();
        	var fecha1 = $("#datepicker1").val();
        	var fecha2 = $("#datepicker2").val();

        	$.ajax({
		      url: 'busquedaMoto',
		      type: 'post',
		      dataType: 'json',
		      data: {
		      	motociclista : motociclista,
		      	fecha1 : fecha1,
		      	fecha2 : fecha2
		      }
		    })
		    .done(function (result) {
		    	console.log(result);
		      	$.each(result, function(index, order) {

		      		var pagoM = parseFloat(order.shipping_charge * 0.70).toFixed(2);
		      		var tipoPago;
		      		
		      		if(order.payment_method_id == 1){
		      			tipoPago = "Efectivo";
		      		}else if(order.payment_method_id == 2){
		      			tipoPago = "Tarjeta";
		      		}else if(order.payment_method_id == 3){
		      			tipoPago = "Tigo Money";
		      		}

	            	//agregamos la linea a la tabla
	                $("#tablaBusqueda>tbody").append('<tr id="fila"><td>'+order.order_cod+'</td><td>'+order.created_at+'</td><td>'+tipoPago+'</td><td>'+order.address_1+'</td><td>'+order.zone+'</td><td>'+order.shipping_charge+'</td><td>'+pagoM+'</td><td>'+order.nombre+'</td></tr>');
	            });
		    })
		    .fail(function (result) {
		    	$("#tablaBusqueda>tbody").append('<tr id="fila"><td colspan="8">Error al cargar los datos</td></tr>');
		    });
        });

		$("#limpiar").click(function(){
			$("#fila").remove();
		});

	</script>
@stop