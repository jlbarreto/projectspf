@extends('general.new_visor')

@section('content')	

	<form class="form-horizontal" action="uploadCsv" method="POST" enctype="multipart/form-data">
		<h2 style="text-align:center;">Subir CSV</h2>
		<br>
	    <div class="form-group">
	    	<div class="col-xs-2"></div>
	        <label class="control-label col-xs-3">CSV:</label>
	        <div class="col-xs-4">
	            <input type="file" accept=".csv" name="archivocsv" id="archivocsv">
	            <input name="MAX_FILE_SIZE" type="hidden" value="20000" />
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