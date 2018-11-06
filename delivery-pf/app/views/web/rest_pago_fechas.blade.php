@extends('general.new_visor')

@section('content')

<div class="table-responsive">
	<h2 style="text-align:center;">Reporte de ventas </h2>
		<div id="selectRes">
			<div class="col-md-2">
				<select name="restauranteSel" class="form-control" id="restauranteSel">
                  	<option value="null">Restaurante</option>
              		@foreach($res as $k => $rest)
                		<option value="{{$rest->restaurant_id}}">{{$rest->name}}</option>
              		@endforeach
                </select>
			</div>
		</div>
		<div id="pago_list">
			<div class="col-md-2">
				<select id="tipo_pago" class="form-control">
					<!--<option value="todos">Todos</option>-->
					<option value="1">Efectivo</option>
					<option value="2">Tarjeta de Crédito</option>
					<option value="3">Tigo Money</option>
				</select>
			</div>
		</div>
		<div id="mostrar_fechas">
			<div class="col-md-3">
				<!--<input type="text" id="datepicker1" class="form-control" placeholder="Fecha 1">-->
				<!--<input type="datetime-local" value="2016-01-01 00:00:00" id="datepicker1" class="form-control">-->
				<input type="text" id="datepicker_r" class="form-control" placeholder="Fecha Inicio">
			</div>
			<div class="col-md-3">
				<!--<input type="text" id="datepicker2" class="form-control" placeholder="Fecha 2">-->
				<!--<input type="datetime-local" value="2016-01-01 00:00:00" id="datepicker2" class="form-control">-->
				<input type="text" id="datepicker_r2" class="form-control" placeholder="Fecha Fin">
			</div>
		</div>		
		
		<button id="generar_reporte_rest_pago" class="btn btn-primary">Buscar</button>
		<button id="limpiar" class="btn btn-danger">Limpiar</button>
		<br><br>
		<div id="formularioExc" style="display:none;">
			{{ Form::open(array('url' => 'exportRestPago')) }}
				{{ Form::hidden('restExp', '', array('id' => 'restExp')) }}
				{{ Form::hidden('fecha1Exp', '', array('id' => 'fecha1Exp')) }}
				{{ Form::hidden('fecha2Exp', '', array('id' => 'fecha2Exp')) }}
				{{ Form::hidden('pagoExp', '', array('id' => 'pagoExp')) }}
				{{ Form::submit('Exportar', array('class' => 'btn btn-success', 'style' => 'float:right; position:relative;'))}}
			{{ Form::close() }}
		</div>
	<br>
	<br>
	<table class="table table-bordered" id="tablaReporte" style="overflow-x: scroll;">
		<thead>
			<tr>
				<th>Fecha</th>
				<th># Orden</th>
				<th>Restaurante</th>
				<th>Tipo Pago</th>
				<th>Monto total</th>
				<th>Costo envío</th>
				<th>Comisión Pidafacil</th>
				<th>Pago Rest</th>
			</tr>
		</thead>
		<tbody id='cuerpoT'></tbody>
	</table>
</div>


@stop