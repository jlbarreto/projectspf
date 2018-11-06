@extends('general.visor_template')
@section('content')

{{ HTML::style('css/menus.css') }}

<style type="text/css">
	.modal-dialog {
	    height: 800px;
	}
</style>

	<div id='cssmenu'>
		<ul>
		   	<li>
		   		<a href='listMoto'>
		   			<span>Lista de Motoristas</span>
		   		</a>
		   	</li>
		   	<li>
		   		<a href='manteMoto'>
		   			<span>Nuevo Motociclista</span>
		   		</a>
		   	</li>
		</ul>
	</div>
	<br>
	<div id="tablaList">
		<table class="table table-hover">
			<thead>
				<th>Motociclista</th>
				<th>Nombre</th>
				<th>Teléfono</th>
				<th>Dirección</th>
				<th colspan="2">Opciones</th>
			</thead>
			<tbody>
				@if(isset($motos))
					@foreach($motos as $key=>$order)
						<tr id="col_{{$order->detalle_id}}">
							<td>{{$order->motorista_id}}</td>
							<td>{{$order->nombres}} {{$order->apellidos}}</td>
							<td>{{$order->telefono}}</td>
							<td>{{$order->direccion}}</td>
							<td>
								<span id="editarM_{{$order->detalle_id}}" class="editar"><i class="fa fa-pencil" aria-hidden="true" style="color:black;"></i></span>
								&nbsp;&nbsp;
								<span id="deleteM_{{$order->detalle_id}}" class="borrar"><i class="fa fa-times" aria-hidden="true" style="color:black;"></i></span>
							</td>
						</tr>
						<div class="modal fade" id="editMoto_{{$order->detalle_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					      	<div class="modal-dialog">
					        	<div class="modal-content">
					          		<div  class="modal-header">
					            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					            		<h4 class="modal-title" id="myModalLabel">
					            			<strong>Editar Motociclista<strong>
					            		</h4>
					          		</div>
					          		<div class="modal-body">
									    <div class="form-group">
									        <label class="control-label col-xs-3">Nombre:</label>
									        <div class="col-xs-9">
									            <input type="text" name="edit_nombres" id="edit_nombres_{{$order->detalle_id}}" class="form-control" required="required">
									        </div>
									    </div>
									    <br><br>
									    <div class="form-group">
									        <label class="control-label col-xs-3">Apellido:</label>
									        <div class="col-xs-9">
									            <input type="text" name="edit_apellidos" id="edit_apellidos_{{$order->detalle_id}}" class="form-control" required="required">
									        </div>
									    </div>
									    <br><br>
									    <div class="form-group">
									        <label class="control-label col-xs-3" >Telefono 1:</label>
									        <div class="col-xs-3">
									            <input type="tel" name="edit_telefono1" id="edit_telefono1_{{$order->detalle_id}}" class="form-control" required="required">
									        </div>
									        <label class="control-label col-xs-3" >Telefono 2:</label>
									        <div class="col-xs-3">
									            <input type="tel" name="edit_telefono2" id="edit_telefono2_{{$order->detalle_id}}" class="form-control">
									        </div>
									    </div>
									    <br><br>
									    <div class="form-group">
									        <label class="control-label col-xs-3" >DUI:</label>
									        <div class="col-xs-3">
									            <input type="text" name="edit_dui" id="edit_dui_{{$order->detalle_id}}" class="form-control" placeholder="DUI" required="required">
									        </div>
									        <label class="control-label col-xs-3" >NIT:</label>
									        <div class="col-xs-3">
									            <input type="text" name="edit_nit" id="edit_nit_{{$order->detalle_id}}" class="form-control" placeholder="NIT" required="required">
									        </div>
									    </div>
									    <br><br>
									    <div class="form-group">
									        <label class="control-label col-xs-3">Dirección:</label>
									        <div class="col-xs-9">
									            <textarea rows="2" name="edit_direccion" id="edit_direccion_{{$order->detalle_id}}" class="form-control" required="required"></textarea>
									        </div>
									    </div>
									    <br><br><br>
									    <div class="form-group">
									        <label class="control-label col-xs-3" >En caso de emergencia llamar a:</label>
									        <div class="col-xs-6">
									            <input type="text" name="edit_nombre_emergencia" id="edit_nombre_emergencia_{{$order->detalle_id}}" class="form-control">
									        </div>
									        <div class="col-xs-3">
									            <input type="tel" name="edit_telefono_emergencia" id="edit_telefono_emergencia_{{$order->detalle_id}}" class="form-control">
									        </div>
									    </div>
					          		</div>
					          		<input type="hidden" id="moto_id">
					          		<div class="modal-footer" style="clear:both;">
					          			<button type="button" id="actualizar_{{$order->detalle_id}}" class="btn btn-success button_150 act" data-dismiss="modal">Actualizar</button>
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

	<div id="borrado" class="modal fade" role="dialog">
	  	<div class="modal-dialog modal-sm">
		    <!-- Modal content-->
		    <div class="modal-content">
		      	<div class="modal-header">
		        	<button type="button" class="close" data-dismiss="modal">&times;</button>
		        	<h4 class="modal-title">Registro Eliminado</h4>
		      	</div>
		      	<div class="modal-body">
			        <p>El motociclista fue eliminado.</p>
		      	</div>
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal" id="closeB">Close</button>
		      	</div>
		    </div>
	  	</div>
	</div>

	<script type="text/javascript">
		
		$(".editar").on('click', function(){
			var id_elemento = $(this).attr('id');
    		var id_detMoto = id_elemento.split("_");

    		$.ajax({
		    	url: 'datosMot',
		        type: 'post',
		        dataType: 'json',
		        data: {
		          detalle_id: id_detMoto[1]
		        }
		    })
		    .done(function (result){
		    	console.log(result);
		    	$("#editMoto_"+result[0].detalle_id).modal('show');
		        $("#edit_nombres_"+result[0].detalle_id).val(result[0].nombres);
		        $("#edit_apellidos_"+result[0].detalle_id).val(result[0].apellidos);
		        $("#edit_telefono1_"+result[0].detalle_id).val(result[0].telefono);
		        $("#edit_telefono2_"+result[0].detalle_id).val(result[0].telefono2);
		        $("#edit_dui_"+result[0].detalle_id).val(result[0].dui);
		        $("#edit_nit_"+result[0].detalle_id).val(result[0].nit);
		        $("#edit_direccion_"+result[0].detalle_id).val(result[0].direccion);
		        $("#edit_nombre_emergencia_"+result[0].detalle_id).val(result[0].emergencia_llamar_a);
		        $("#edit_telefono_emergencia_"+result[0].detalle_id).val(result[0].telefono_emergencia);
		        $("#moto_id").val(result[0].motorista_id);
		    })
		    .fail(function (result){
		        alert("No se pudo realizar la acción, por favor intenta de nuevo");
		    });
		});

		$(".act").on('click', function(){
			var id_elemento = $(this).attr('id');
    		var id_detMoto = id_elemento.split("_");
    		
    		var nombres = $("#edit_nombres_"+id_detMoto[1]).val();
    		var apellidos = $("#edit_apellidos_"+id_detMoto[1]).val();
    		var telefono1 = $("#edit_telefono1_"+id_detMoto[1]).val();
    		var telefono2 = $("#edit_telefono2_"+id_detMoto[1]).val();
    		var dui = $("#edit_dui_"+id_detMoto[1]).val();
    		var nit = $("#edit_nit_"+id_detMoto[1]).val();
    		var direccion = $("#edit_direccion_"+id_detMoto[1]).val();
    		var nombre_emergencia = $("#edit_nombre_emergencia_"+id_detMoto[1]).val();
    		var telefono_emergencia = $("#edit_telefono_emergencia_"+id_detMoto[1]).val();
    		var moto_id = $("#moto_id").val();

    		$.ajax({
		    	url: 'editarMot',
		        type: 'post',
		        dataType: 'json',
		        data: {
		          detalle_id: id_detMoto[1],
		          nombres : nombres,
		          apellidos : apellidos,
		          telefono1 : telefono1,
		          telefono2 : telefono2,
		          dui : dui,
		          nit : nit,
		          direccion : direccion,
		          nombre_emergencia : nombre_emergencia,
		          telefono_emergencia : telefono_emergencia,
		          moto_id : moto_id
		        }
		    })
		    .done(function (result){
		    	alert('Motociclista actualizado correctamente');
		    	location.reload();
		    })
		    .fail(function (result){
		        alert("No se pudo realizar la acción, por favor intenta de nuevo");
		    });
		});

		$(".borrar").on('click', function(){
			var id_elemento = $(this).attr('id');
    		var id_detMoto = id_elemento.split("_");

    		var r = confirm("¿Desea eliminar este registro?");
			if (r == true) {
			    $.ajax({
			    	url: 'deleteMoto',
			        type: 'post',
			        dataType: 'json',
			        data: {
			          detalle_id: id_detMoto[1]
			        }
			    })
			    .done(function (result){
			    	$("#borrado").modal('show');
			    })
			    .fail(function (result){
			        alert("No se pudo realizar la acción, por favor intenta de nuevo");
			    });
			}else{
			}
		});

		$("#closeB").click(function(){
			location.reload();
		});
	
	</script>
	
@stop