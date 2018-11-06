@extends('general.new_visor')

@section('content')
	
<div class="table-responsive">
	<h2 style="text-align:center;">Reporte de IVA</h2>
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
				<input type="text" id="datepicker11" class="form-control" placeholder="Fecha Inicio">
			</div>
			<div class="col-md-3">
				<!--<input type="text" id="datepicker2" class="form-control" placeholder="Fecha 2">-->
				<!--<input type="datetime-local" value="2016-01-01 00:00:00" id="datepicker2" class="form-control">-->
				<input type="text" id="datepicker22" class="form-control" placeholder="Fecha Fin">
			</div>
		</div>
		<div id="mes_list" style="display:none;">
			<div class="col-md-2">
				<select id="meses1" class="form-control">
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
				<select id="anio1" class="form-control">
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
		
		<button id="generarReporteIva" class="btn btn-primary">Buscar</button>
		<button id="limpiar" class="btn btn-danger">Limpiar</button>
		<br><br>
		<div id="formularioExc" style="display:none;">
			{{ Form::open(array('url' => 'exportIva')) }}
				{{ Form::hidden('motivoiva', '', array('id' => 'motivoiva')) }}
				{{ Form::hidden('fechaiva1', '', array('id' => 'fechaiva1')) }}
				{{ Form::hidden('fechaiva2', '', array('id' => 'fechaiva2')) }}
				{{ Form::hidden('mesiva', '', array('id' => 'mesiva')) }}				
				{{ Form::hidden('anioiva', '', array('id' => 'anioiva')) }}
				{{ Form::hidden('restiva', '', array('id' => 'restiva')) }}
				{{ Form::submit('Exportar', array('class' => 'btn btn-success', 'style' => 'float:right; position:relative;'))}}
			{{ Form::close() }}
		</div>
	<br>
	<br>
	<table class="table table-bordered" id="tablaReporte" style="overflow-x: scroll;">
		<thead>
			<tr>
				<th>Fecha</th>
				<th>Restaurante</th>
				<th>Monto total pagado</th>
				<th>IVA a cobrar</th>
				<th>Valor Restaurante</th>				
			</tr>
		</thead>
		<tbody id='cuerpoT'></tbody>
	</table>
</div>

@stop