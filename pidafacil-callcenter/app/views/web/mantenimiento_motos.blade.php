@extends('general.visor_template')
@section('content')

{{ HTML::style('css/menus.css') }}

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
	<div id="formulario">
		<form class="form-horizontal" action="newMoto" method="POST" enctype="multipart/form-data">
			<h2 style="text-align:center;">Ingresar Datos de Motociclista</h2>
		    <div class="form-group">
		        <label class="control-label col-xs-3">Nombre:</label>
		        <div class="col-xs-9">
		            <input type="text" name="nombres" id="nombres" class="form-control" placeholder="Nombres" required="required">
		        </div>
		    </div>
		    <div class="form-group">
		        <label class="control-label col-xs-3">Apellido:</label>
		        <div class="col-xs-9">
		            <input type="text" name="apellidos" id="apellidos" class="form-control" placeholder="Apellidos" required="required">
		        </div>
		    </div>
		    <div class="form-group">
		        <label class="control-label col-xs-3" >Telefono 1:</label>
		        <div class="col-xs-3">
		            <input type="tel" name="telefono1" id="telefono1" class="form-control" placeholder="Teléfono 1" required="required">
		        </div>
		        <label class="control-label col-xs-3" >Telefono 2:</label>
		        <div class="col-xs-3">
		            <input type="tel" name="telefono2" id="telefono2" class="form-control" placeholder="Teléfono 2">
		        </div>
		    </div>
		    <div class="form-group">
		        <label class="control-label col-xs-3" >DUI:</label>
		        <div class="col-xs-3">
		            <input type="text" name="dui" id="dui" class="form-control" placeholder="DUI" required="required">
		        </div>
		        <label class="control-label col-xs-3" >NIT:</label>
		        <div class="col-xs-3">
		            <input type="text" name="nit" id="nit" class="form-control" placeholder="NIT" required="required">
		        </div>
		    </div>
		    <div class="form-group">
		        <label class="control-label col-xs-3">Dirección:</label>
		        <div class="col-xs-9">
		            <textarea rows="2" name="direccion" id="direccion" class="form-control" placeholder="Dirección" required="required"></textarea>
		        </div>
		    </div>
		    <div class="form-group">
		        <label class="control-label col-xs-3" >En caso de emergencia llamar a:</label>
		        <div class="col-xs-6">
		            <input type="text" name="nombre_emergencia" id="nombre_emergencia" class="form-control" placeholder="Nombre">
		        </div>
		        <div class="col-xs-3">
		            <input type="tel" name="telefono_emergencia" id="telefono_emergencia" class="form-control" placeholder="Teléfono">
		        </div>
		    </div>
		    <div class="form-group">
		    	<label class="control-label col-xs-3">Foto dui:</label>
		        <div class="col-xs-4">
		            <input type="file" name="img_dui" id="img_dui">
		        </div>
		    </div>
		    <div class="form-group">
		    	<label class="control-label col-xs-3">Foto Licencia:</label>
		        <div class="col-xs-4">
		            <input type="file" name="img_lic" id="img_lic">
		        </div>
		    </div>
		    <div class="form-group">
		    	<label class="control-label col-xs-3">Foto antecedentes penales:</label>
		        <div class="col-xs-4">
		            <input type="file" name="img_antecedentes" id="img_antecedentes">
		        </div>
		    </div>
		    <div class="form-group">
		    	<label class="control-label col-xs-3">Foto solvencia pnc:</label>
		        <div class="col-xs-4">
		            <input type="file" name="img_solvencia" id="img_solvencia">
		        </div>
		    </div>
		    <div class="form-group">
		    	<label class="control-label col-xs-3">Motociclista asignado:</label>
		    	<div class="col-xs-4">
		    		<select name="motoSelect" class="form-control" id="motoSelect" style="width: auto;">
	                	<option value="0">Asignar a motorista</option>
                  		@foreach($motoristas as $k => $motorista)
                    		<option value="{{$motorista->motorista_id}}">{{$motorista->nombre}}</option>
                  		@endforeach
	                </select>
		    	</div>
		    </div>
		    <br>
		    <div class="form-group">
		        <div class="col-xs-offset-5 col-xs-9">
		            <input type="submit" class="btn btn-success" value="Enviar">
		            <input type="reset" class="btn btn-default" value="Limpiar">
		        </div>
		    </div>
		</form>
	</div>
	
@stop