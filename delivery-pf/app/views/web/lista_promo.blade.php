@extends('general.new_visor')

@section('content')	
	
	<style type="text/css">
		.modal-dialog {
		    height: 500px;
		}
	</style>

	<h2 style="text-align:center;">Configurar Promoción</h2>
	<br>	
	<div id='cssmenu'>
		<ul>
		   	<li>
		   		<a href='listPromo'>
		   			<span>Lista de Promociones</span>
		   		</a>
		   	</li>
		   	<li>
		   		<a href='promo'>
		   			<span>Nueva Promoción</span>
		   		</a>
		   	</li>
		</ul>
	</div>
	<br>

	<div id="tablaList">
		<table class="table table-hover">
			<thead>
				<th>Porcentaje</th>
				<th>Activo</th>
				<th colspan="2">Opciones</th>
			</thead>
			<tbody>
				@if(isset($result) && $result != 'null')
					@foreach($result as $key => $order)
						<tr id="col_{{$order->id_config}}">
							<td>{{$order->porcentaje}}%</td>
							@if($order->activate == 0)
								<td>Inactivo</td>
							@else
								<td>Activo</td>
							@endif
							<td>
								<span id="editarM_{{$order->id_config}}" class="editar"><i class="fa fa-pencil" aria-hidden="true" style="color:black;"></i></span>
								&nbsp;&nbsp;
								<span id="deleteM_{{$order->id_config}}" class="borrar"><i class="fa fa-times" aria-hidden="true" style="color:black;"></i></span>
							</td>
						</tr>

						<div class="modal fade" id="editPromo_{{$order->id_config}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					      	<div class="modal-dialog modal-sm">
					        	<div class="modal-content">
					          		<div  class="modal-header">
					            		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					            		<h4 class="modal-title" id="myModalLabel">
					            			<strong>Editar Promoción<strong>
					            		</h4>
					          		</div>
					          		<div class="modal-body">
									    <div class="form-group">
									        <label class="control-label col-xs-4">Porcentaje:</label>
									        <div class="col-xs-6">
									            <input type="text" name="edit_porcentaje" id="edit_porcentaje_{{$order->id_config}}" class="form-control" required="required">
									        </div>
									    </div>
									    <br><br>
									    <div class="form-group">
									        <label class="control-label col-xs-3">Activo:</label>
						        	        <div class="col-xs-9">
									        	<label class="control-label col-xs-2">Si</label>
									            <input class="control-label col-xs-1 rad1" type="radio" name="activate_{{$order->id_config}}" id="activate_porcentaje_{{$order->id_config}}" value="1" style="width: 40px; height: 25px;">
									        	
									        	<label class="control-label col-xs-2">No</label>
									            <input class="control-label col-xs-1 rad2" type="radio" name="activate_{{$order->id_config}}" id="activate_porcentaje_{{$order->id_config}}" value="0" style="width: 40px; height: 25px;">
									        </div>
									    </div>
					          		</div>
					          		<br>
					          		<div class="modal-footer" style="clear:both;">
					          			<button type="button" id="actualizar_{{$order->id_config}}" class="btn btn-success button_150 act" data-dismiss="modal">Actualizar</button>
					            		<button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
					          		</div>
					        	</div>
					      	</div>
					    </div>
					@endforeach				
				@else
					<tr>
						<td colspan="2" style="text-align: center">No hay resultados</td>
					</tr>	
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
			        <p>La promoción fue eliminada.</p>
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
    		var id_promo = id_elemento.split("_");

    		$.ajax({
		    	url: 'datosPromo',
		        type: 'post',
		        dataType: 'json',
		        data: {
		          	promo_id: id_promo[1]
		        }
		    })
		    .done(function (result){
		    	console.log(result);

		    	$("#editPromo_"+result[0].id_config).modal('show');
		        $("#edit_porcentaje_"+result[0].id_config).val(result[0].porcentaje);

		        if(result[0].activate == 0){
		        	$(".rad2").prop("checked", true);
		        }else if(result[0].activate == 1){
		        	$(".rad1").prop("checked", true);
		        }		        
		    })
		    .fail(function (result){
		        alert("No se pudo realizar la acción, por favor intenta de nuevo");
		    });
		});

		$(".act").on('click', function(){
			var id_elemento = $(this).attr('id');
    		var id_promo = id_elemento.split("_");
    		
    		var porcentaje = $("#edit_porcentaje_"+id_promo[1]).val();
    		var activate = $('input[name=activate_'+id_promo[1]+']:checked').val();

    		console.log(id_promo+' '+porcentaje+' '+activate);

    		$.ajax({
		    	url: 'editarPromo',
		        type: 'post',
		        dataType: 'json',
		        data: {
		          	id_promo: id_promo[1],
		          	porcentaje : porcentaje,
		          	activate : activate		          
		        }
		    })
		    .done(function (result){
		    	alert('Promoción actualizada correctamente');
		    	location.reload();
		    })
		    .fail(function (result){
		        alert("No se pudo realizar la acción, por favor intenta de nuevo");
		    });
		});

		$(".borrar").on('click', function(){
			var id_elemento = $(this).attr('id');
    		var id_config = id_elemento.split("_");

    		var r = confirm("¿Desea eliminar este registro?");
			if (r == true) {
			    $.ajax({
			    	url: 'deletePromo',
			        type: 'post',
			        dataType: 'json',
			        data: {
			          	id_promo: id_config[1]
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