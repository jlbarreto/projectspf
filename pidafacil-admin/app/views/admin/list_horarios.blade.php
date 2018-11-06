@extends('general.admin_layout')
@section('content')

<div class="container" style="padding:2%;">
	<div class="table-responsive">
	  	<table class="table" style="color:white;">
	    	<thead>
	    		<th>Restaurante</th>
	    		<th>Día</th>
	    		<th>Hora Cierre</th>
	    		<th>Hora Apertura</th>
	    		<th>Opciones</th>
	    	</thead>
	    	<tbody>
	    		@if(count($horarios) > 0)
			    	@foreach($horarios as $hora)
			    		<tr>
				    		<td>{{$hora['name']}}</td>
				    		<td>{{$hora['dia']}}</td>
				    		<td>{{$hora['closing_time']}}</td>
				    		<td>{{$hora['opening_time']}}</td>
				    		<td>&nbsp;&nbsp;
				    			<i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="modal" data-target="#edit_horario_{{$hora['schedules_options_id']}}"></i>
				    			&nbsp;&nbsp;&nbsp;&nbsp;
				    			<i class="fa fa-times" aria-hidden="true" data-toggle="modal" data-target="#delete_horario_{{$hora['schedules_options_id']}}"></i>
				    		</td>
			    		</tr>
			    		<!--MODAL PARA EDITAR UN HORARIO-->
			    		<div id="edit_horario_{{$hora['schedules_options_id']}}" class="modal fade" role="dialog">
						  	<div class="modal-dialog">
						    	<!-- Modal content-->
							    <div class="modal-content">
							      	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							        	<h4 class="modal-title">Editar Horario</h4>
							      	</div>
							      	<div class="modal-body" style="color:black; text-align:center;">
							      		<script type="text/javascript">
							      			$(function() {
												$("#diaSel_{{$hora['schedules_options_id']}} option[value='{{$hora['day_id']}}']").attr("selected", "selected");
												$("#horaCierre_{{$hora['schedules_options_id']}} option[value='{{$hora['closing_time']}}']").attr("selected", "selected");
												$("#horaApertura_{{$hora['schedules_options_id']}} option[value='{{$hora['opening_time']}}']").attr("selected", "selected");
											});
							      		</script>
							        	<p style="font-weight:bold;">Actualizar horario</p>
							        	<div class="row">
							        		<div class="col-md-2"></div>
							        		<div class="col-md-3">
							        			Restaurante:
							        		</div>
							        		<div class="col-md-3">
							        			<select name="restauranteSel" class="form-control" id="restauranteSel_{{$hora['schedules_options_id']}}" style="background:transparent;">
											      	<option value="{{$hora['restaurant_id']}}">{{$hora['name']}}</option>
											    </select>
							        		</div>
							        	</div>
							        	<br>
							        	<div class="row">
							        		<div class="col-md-2"></div>
							        		<div class="col-md-3">
							        			Día:
							        		</div>
							        		<div class="col-md-3">
							        			<select name="diaSel" class="form-control" id="diaSel_{{$hora['schedules_options_id']}}" style="background:transparent;">
											      	<option value="1">Domingo</option>
											      	<option value="2">Lunes</option>
											      	<option value="3">Martes</option>
											      	<option value="4">Miércoles</option>
											      	<option value="5">Jueves</option>
											      	<option value="6">Viernes</option>
											      	<option value="7">Sábado</option>
											    </select>
							        		</div>
							        	</div>
							        	<br>
							        	<div class="row">
							        		<div class="col-md-2"></div>
							        		<div class="col-md-3">
							        			Hora de Cierre:
							        		</div>
							        		<div class="col-md-3">							        			
											    <input type="text" name="horaCierre" class="form-control timepickerC2" id="horaCierre_{{$hora['schedules_options_id']}}" style="background:transparent; color:black;"/>
							        		</div>
							        	</div>
							        	<br>
							        	<div class="row">
							        		<div class="col-md-2"></div>
							        		<div class="col-md-3">
							        			Hora de Apertura:
							        		</div>
							        		<div class="col-md-3">							        			
											    <input type="text" name="horaApertura" class="form-control timepickerA2" id="horaApertura_{{$hora['schedules_options_id']}}" style="background:transparent; color:black;"/>
							        		</div>
							        	</div>
							      	</div>
							      	<div class="modal-footer">
							      		<button type="button" class="btn btn-success actualizarHor" data-dismiss="modal" id="actualizarHor_{{$hora['schedules_options_id']}}">Actualizar</button>
							        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      	</div>
							    </div>
						  	</div>
						</div>
						<!--MODAL PARA ELIMINAR UN HORARIO-->
						<div id="delete_horario_{{$hora['schedules_options_id']}}" class="modal fade" role="dialog">
						  	<div class="modal-dialog modal-sm">
						    	<!-- Modal content-->
							    <div class="modal-content">
							      	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							        	<h4 class="modal-title">Eliminar Horario</h4>
							      	</div>
							      	<div class="modal-body">
							        	<p>¿Está seguro de eliminar este registro?</p>
							      	</div>
							      	<div class="modal-footer">
							      	<button type="button" class="btn btn-success eliminarHor" data-dismiss="modal" id="eliminarHor_{{$hora['schedules_options_id']}}">Eliminar</button>
							        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      	</div>
							    </div>
						  	</div>
						</div>
			    	@endforeach
			    @else
			    	<tr>
			    		<th colspan="5" style="text-align:center;">No hay registros</th>
			    	</tr>
			    @endif
	    	</tbody>
	  	</table>
	  	@if(count($horarios) > 0)
	  		{{$horarios->links()}}
	  	@endif
	</div>
</div>

<script type="text/javascript">
	$(".actualizarHor").click(function(){
		var $btn = $(this);
	    var id_elemento = $(this).attr('id');
	    var id_registro = id_elemento.split("_");

		var restaurante = $("#restauranteSel_"+id_registro[1]).val();
		var dia = $("#diaSel_"+id_registro[1]).val();
		var hora_c = $("#horaCierre_"+id_registro[1]).val();
		var hora_a = $("#horaApertura_"+id_registro[1]).val();
        $.ajax({
	      	url: '{{ URL::to("/admin/restaurant/edit_schedules")}}',
	      	type: 'post',
	      	dataType: 'json',
	      	data: {
	      		registro: id_registro[1],
	            restaurante: restaurante,
	            dia: dia,
	            hora_c: hora_c,
	            hora_a: hora_a
	        }
	    })
	    .done(function(result){
	    	alert('Registro modificado correctamente');
	    	location.reload();
	      	console.log(result);
	    })
	    .fail(function(result){
	      	alert("No se pudo modificar el registro.");
	      	console.log(result);
	    });
	});

	$(".eliminarHor").click(function(){
		var $btn = $(this);
	    var id_elemento = $(this).attr('id');
	    var id_registro = id_elemento.split("_");
        $.ajax({
	      	url: '{{ URL::to("/admin/restaurant/delete_schedule")}}',
	      	type: 'post',
	      	dataType: 'json',
	      	data: {
	      		registro: id_registro[1]
	        }
	    })
	    .done(function(result){
	    	alert('Registro eliminado correctamente');
	    	location.reload();
	      	console.log(result);
	    })
	    .fail(function(result){
	      	alert("No se pudo eliminar el registro.");
	      	console.log(result);
	    });
	});
</script>

@stop