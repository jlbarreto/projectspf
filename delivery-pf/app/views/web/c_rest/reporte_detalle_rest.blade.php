@extends('general.new_visor')

@section('content')

<div class="table-responsive">
	<h2 style="text-align:center;">Listado con detalle de ventas</h2>
		<div class="col-md-2"><span>Búsqueda por fechas:</span></div>
		<!--<div class="col-md-3">
			<select id="motivo_busqueda" class="form-control">
				<option value="none">Elija una opción</option>
				<option value="fecha">Rango de Fechas</option>
				<option value="moto">Por Motociclista</option>
				<option value="pago">Por tipo de Pago</option>
			</select>
		</div>-->
		<div id="mostrar_fechas">
			<div class="col-md-3">
				<!--<input type="text" id="datepicker1" class="form-control" placeholder="Fecha 1">-->
				<!--<input type="datetime-local" value="2016-01-01 00:00:00" id="datepicker1" class="form-control">-->
				<input type="text" id="datepicker1" class="form-control" placeholder="Fecha Inicio">
			</div>
			<div class="col-md-3">
				<!--<input type="text" id="datepicker2" class="form-control" placeholder="Fecha 2">-->
				<!--<input type="datetime-local" value="2016-01-01 00:00:00" id="datepicker2" class="form-control">-->
				<input type="text" id="datepicker2" class="form-control" placeholder="Fecha Fin">
			</div>
		</div>
		<div id="moto_list" style="display:none;">
			<div class="col-md-3">
				<select name="moto" class="form-control" id="moto">
                  	<option value="todos">Todos</option>
              		@foreach($motoristas as $k => $motorista)
                		<option value="{{$motorista->motorista_id}}">{{$motorista->nombre}}</option>
              		@endforeach
                </select>
			</div>
		</div>
		<div id="pago_list" style="display:none;">
			<div class="col-md-2">
				<select id="tipo_pago" class="form-control">
					<option value="todos">Todos</option>
					<option value="1">Efectivo</option>
					<option value="2">Tarjeta de Crédito</option>
					<option value="3">Tigo Money</option>
				</select>
			</div>
		</div>
		<button id="BVentaDetalle_rest" class="btn btn-primary">Buscar</button>
		<button id="limpiar" class="btn btn-danger">Limpiar</button>
	<br>
	<br>
	<table class="table table-bordered" id="tablaReporte_rest">
		<thead>
			<tr>
				<th>Orden</th>
				<th>Fecha</th>
				<th>Estado</th>
				<th>Tipo Pago</th>
				<th>Pago a Restaurante</th>
				<th>Detalle</th>
			</tr>
		</thead>
		<tbody id="cuerpoT"></tbody>
	</table>
</div>

<div id="detalleOrden_rest" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg">
	    <!-- Modal content-->
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	<h4 class="modal-title"><strong>Código de Orden: <span id="cod_orden_r"></span></strong></h4>
	      	</div>
	      	<div class="modal-body">
		      	<div class="row">
		          	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		                <h1><span>Usuario Delivery</span></h1>
		                <!--<p id="ord_address"></p>-->
		                <!--<p>Referencia: <span id="ord_ref"></span></p>-->
						<p>Zona: <span id="ord_zone_r"></span></p>
		                <p><strong>Fecha:</strong> <span id="ord_date_r"></span></p>
		                <!--<p><strong>Tiempo Total: </strong><span id="tot_tie_r"></span></p>-->
		          	</div>
		          	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
		           		<h1 style="width:100%;" id="ord_total_r"></h1>
		          	</div>
		    	</div>
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
		      	<div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                	<div class="col-md-3" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  		$ <span id="pago_rest_r"></span>
                	</div>
                	
                	<div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  		$ <span id="costo_envio_r"></span>
                	</div>
                	<div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  		$ <span id="total_cust_r"></span>
                	</div>
                	<div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  		$ <span id="cambio_r"></span>
                	</div>
              	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      	</div>
	    </div>
	</div>
</div>

@stop

