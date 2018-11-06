@extends('general.new_visor')

@section('content')	

	<form class="form-horizontal" action="addPromo" method="POST" enctype="multipart/form-data">
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
		<br>
	    <div class="form-group">
	    	<div class="col-xs-2"></div>
	        <label class="control-label col-xs-3">Porcentaje:</label>
	        <div class="col-xs-3">
	            <input type="text" name="porcentaje" id="porcentaje" class="form-control" placeholder="Porcentaje en int o float" required="required" style="display: -webkit-inline-box !important; width: 90% !important;"> %
	        </div>	        
	    </div>
	    <div class="form-group">
	    	<div class="col-xs-2"></div>
	        <label class="control-label col-xs-3">Activar:</label>
	        <div class="col-xs-3">
	        	<label class="control-label col-xs-2">Si</label>
	            <input class="control-label col-xs-2" type="radio" name="activate" id="activate" value="1" style="width: 25px; height: 25px;">
	        	
	        	<label class="control-label col-xs-2">No</label>
	            <input class="control-label col-xs-2" type="radio" name="activate" id="activate" value="0" style="width: 25px; height: 25px;">
	        </div>
	    </div>
	    <div class="form-group">
	        <div class="col-xs-offset-5 col-xs-8">
	            <input type="submit" class="btn btn-success" value="Enviar">
	            <!--<input type="reset" class="btn btn-default" value="Limpiar">-->
	        </div>
	    </div>
	</form>
@stop