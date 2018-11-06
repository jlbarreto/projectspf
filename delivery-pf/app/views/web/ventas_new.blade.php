@extends('general.new_visor')

@section('content')

<div class="table-responsive">
	<h2 style="text-align:center;">Listado de Ordenes</h2>
		<span>Tipo de Reporte:</span>
		<br><br>
		<div class="col-md-3">
			<select id="motivo_busqueda" class="form-control">
				<option value="none">Elija una opción</option>
				<option value="fecha">Rango de Fechas</option>
				<option value="mes">Por Mes</option>
				<!--<option value="restaurant">Por Restaurante</option>-->
			</select>
		</div>
		<div id="mostrar_fechas" style="display:none;">
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
		<div id="mes_list" style="display:none;">
			<div class="col-md-2">
				<select id="meses" class="form-control">
					<option value="todos">Todos</option>
					<option value="01">Enero</option>
					<option value="02">Febrero</option>
					<option value="03">Marzo</option>
					<option value="04">Abril</option>
					<option value="05">Mayo</option>
					<option value="06">Junio</option>
					<option value="07">Julio</option>
					<option value="08">Agosto</option>
					<option value="09">Septiembre</option>
					<option value="10">Octubre</option>
					<option value="11">Noviembre</option>
					<option value="12">Diciembre</option>
				</select>
				<!--<input type="text" id="mespicker" class="form-control">-->
			</div>
			<div class="col-md-2">
				<select id="anio" class="form-control">
					<option value="2015">2015</option>
					<option value="2016" selected="selected">2016</option>
					<option value="2017">2017</option>
				</select>
				<!--<input type="text" id="mespicker" class="form-control">-->
			</div>
		</div>
		<div id="selectRes" style="display:none;">
			<div class="col-md-3">
				<select name="restauranteSel" class="form-control" id="restauranteSel">
                  	<option value="todos">Todos los Restaurantes</option>
              		@foreach($restaurantes as $k => $rest)
                		<option value="{{$rest->restaurant_id}}">{{$rest->name}}</option>
              		@endforeach
                </select>
			</div>
		</div>
		<button id="generarReporteV" class="btn btn-primary">Buscar</button>
		<button id="limpiar" class="btn btn-danger">Limpiar</button>
		<br><br>
		<div id="formularioExc" style="display:none;">
			{{ Form::open(array('url' => 'exportRep')) }}
				{{ Form::hidden('motivo2', '', array('id' => 'motivo2')) }}
				{{ Form::hidden('fechaN1', '', array('id' => 'fechaN1')) }}
				{{ Form::hidden('fechaN2', '', array('id' => 'fechaN2')) }}
				{{ Form::hidden('mesN', '', array('id' => 'mesN')) }}
				{{ Form::hidden('RestN', '', array('id' => 'RestN')) }}
				{{ Form::hidden('AnioN', '', array('id' => 'AnioN')) }}
				<span id="textoRest" style="color:black; float:left; font-weight: bold;"></span>
				{{ Form::submit('Exportar', array('class' => 'btn btn-success', 'style' => 'float:right; position:relative;'))}}
			{{ Form::close() }}
		</div>
	<br>
	<br>
	<table class="table table-bordered" id="tablaReporte" style="overflow-x: scroll;">
		<thead>
			<tr>
				<th>Fecha</th>
				<th>Pago Restaurante</th>
				<th>Costo Envío</th>
				<th>Comisión Restaurante</th>
				<th>Comisión Envío</th>
				<th>Total Comisiones</th>
				<th>Total Venta</th>
				<th>Núm. Pedidos</th>
			</tr>
		</thead>
		<tbody id='cuerpoT'></tbody>
	</table>
</div>

@stop

