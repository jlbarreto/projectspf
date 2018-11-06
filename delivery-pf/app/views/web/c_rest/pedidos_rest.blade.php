@extends('general.new_visor')

@section('content')
	<div class="table-responsive">
		<h2 style="text-align:center;">Listado de Ordenes</h2>
		<br>
		<table class="table">
			<tr>
				<th># Orden</th>
				<th>Pago</th>
				<th>Direccion Cliente</th>
				<th>Zona</th>
				<th>Restaurante</th>
				<th>Motorista</th>
				<th>Estado</th>
				<th style="text-align:center;">Detalle</th>
			</tr>
			@if(isset($orders))
              	@foreach($orders as $key=>$order)
              		<?php
              			$estado_ord = DB::select('
								SELECT os.order_status_id
								FROM req_order_status_logs as os INNER JOIN req_orders as ro ON os.order_id= ro.order_id WHERE os.order_id = ? order by os.created_at DESC limit 1' , array($order->order_id));
              		?>
              		@if($estado_ord[0]->order_status_id < 5)
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
								    <p class="fa fa-clock-o" aria-hidden="true" style="color:#70bccc;"> {{ $horaFinalCo2 }}</p>
								</td>
		              		@endif
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
						    <td>
						     	@for($i=0;$i<count($res);$i++)
						      		@if($order->restaurant_id == $res[$i]->restaurant_id)
								     	<?php
								      		$aloId = $res[$i]->orders_allocator_id;
								      	?>
						      			@for($j=0;$j<count($res);$j++)
						      				@if($aloId == $res[$j]->restaurant_id)
						      					{{ $res[$j]->name }} <br />{{ $res[$j]->address }}
						      				@endif
						      			@endfor
						      		@endif
						      	@endfor
						    </td>
						    <?php
						        $nombre_mt = $order->order_id;
						        $motorista_name = DB::select('select mo.nombre FROM pf.motoristas as mo inner join pf.req_order_motorista as rm
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
						    	<button id="order_id_{{ $order->order_id }}" class="btn btn-primary mostrarDetalle">Ver detalle</button>
						    </td>
		              	</tr>
					@endif		              	
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
						            <div class="row">
						              	<div class="col-lg-12">
						                	<h3>Detalle de los productos</h3>
						              	</div>
						            </div>
					            	<!--<div id="contenedorH" style="overflow-x: scroll;"><-->
					              	<div class="row" style="border-top:solid 1px #cccccc;">
						                <div class="col-md-1" id="responsive_div1" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  Qty
						                </div>
						                <div class="col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  Producto
						                </div>
						                <div class="col-md-2" id="responsive_div3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  Condiciones
						                </div>
						                <div class="col-md-2" id="responsive_div4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  Ingredientes
						                </div>
						                <div class="col-md-2" id="responsive_div5" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  Comentario
						                </div>
						                <div class="col-md-1" id="precioOc2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  Precio Unidad
						                </div>
						                <div class="col-md-1" id="precioOc2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
						                  Precio Total
						                </div>
					              	</div>
						            @foreach($order->products as $ke=>$products)
						              	@foreach($products->conditions as $k=>$condition)
						                	<?php $conditions = $condition; ?>
						              	@endforeach
						              	@foreach($products->ingredients as $k=>$v)
						                	<?php $ingredients = $v; ?>
						              	@endforeach
						              	<div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
						                	<div class="col-md-1" id="responsive_div1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
						                  		<br>
						                  		{{ $products->quantity }}
						                	</div>
						                <div class="col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
						                  	<br>
						                  	{{ $products->product }}
						                </div>
						                <div class="col-md-2" id="responsive_div3" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
						                  	@if(isset($products->conditions) && count($products->conditions) > 0)
						                    	@foreach($products->conditions as $k=>$condition)
						                      		@for($i = 0; $i < count($condi);$i++)
						                        		@if($condi[$i]->condition_id == $condition->condition_id)
						                        			{{ $condition->condition}}: {{ $condition->condition_option }} <br />
						                        		@endif
						                      		@endfor
						                    	@endforeach
						                  	@else
						                    	No tiene condiciones
						                  	@endif
						                </div>
						                <div class="col-md-2" id="responsive_div4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;overflow-y: scroll;">
						                  	@if(isset($products->ingredients) && count($products->ingredients) > 0)
						                    	@foreach($products->ingredients as $k=>$v)
						                      		@if($v->remove == 0)
						                        		{{ $v->ingredient }}<br />
						                      		@endif
						                    	@endforeach
						                  	@else
						                    	No tiene ingredientes
						                  	@endif
						                </div>
						                <div class="col-md-2" id="responsive_div5" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
						                  	@if($products->comment=='')
						                    	No hay ningún comentario
						                   	@else
						                    	{{$products->comment}}
						                  	@endif
						                </div>
						                <div class="col-md-1" id="precioOc" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
						                  	<br>
						                  	$ {{ $products->unit_price }}
						                </div>
						                <div class="col-md-1" id="precioOc" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
						                  	<br>
						                  	$ {{ $products->total_price }}
						                </div>
						              </div>
						            @endforeach
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
		</table>
	</div>
@stop
