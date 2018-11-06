@extends('general.admin_layout')
@section('content')

<div class="container">
	<!--<form class="hora_rest" action="admin/restaurant/new_schedules">-->
	{{ Form::open(array('url' => 'admin/restaurant/new_schedules', 'class'=>'hora_rest')) }}
		<div class="row">
			<h2 id="letra" style="text-align:center;">Definir horarios de Restaurantes</h2>
			<div class="col-md-3">
				<h3 id="letra">Elegir Restaurante</h3>
				<select name="restauranteSel" class="form-control" id="restauranteSel" style="background:transparent; color:black;">
			      	<option value="none">Seleccione..</option>
			  		@foreach($restaurants as $k => $rest)
			    		<option value="{{$rest->restaurant_id}}">{{$rest->name}}</option>
			  		@endforeach
			    </select>
			</div>
			<div class="col-md-3">
				<h3 id="letra">Elegir Día</h3>
				<select name="diaSel" class="form-control" id="diaSel" style="background:transparent; color:black;">
			      	<option value="none">Seleccione..</option>
			      	<option value="1">Domingo</option>
			      	<option value="2">Lunes</option>
			      	<option value="3">Martes</option>
			      	<option value="4">Miércoles</option>
			      	<option value="5">Jueves</option>
			      	<option value="6">Viernes</option>
			      	<option value="7">Sábado</option>
			    </select>
			</div>
			<div class="col-md-3">
				<h3 id="letra">Hora Cierre</h3>
				<div class="form-group">
                    <input type="text" name="horaCierre" class="form-control timepickerC" id="horaCierre" style="background:transparent; color:black;"/>
                </div>
			</div>
			<div class="col-md-3">
				<h3 id="letra">Hora Apertura</h3>
				<input type="text" name="horaApertura" class="form-control timepickerA" id="horaApertura" style="background:transparent; color:black;"/>				
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-5"></div>
			<div class="col-md-2">
				<button class="btn btn-success btn-lg" style="float:left;">Actualizar Horario</button>
			</div>
		</div>
	{{Form::close()}}
	<!--</form>-->
</div>
@stop