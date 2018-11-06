@extends('general.useraddress_page')
@section('fContent')
<?php $i=0 ?>
@foreach($addresses as $key => $value)
	@if(empty($value))<?php continue; ?>
	@else
		<?php $address[] = $value ?>
	@endif
@endforeach
<nav class="navbar navbar-inverse" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
            data-target=".navbar-ex1-collapse">
     	<i class="fa fa-bars fa-2x"></i>
    </button>
	<a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
  </div>
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Inicio <b class="caret"></b></a>
				<ul class="dropdown-menu">
				<li><a href="{{ URL::to('/') }}">Inicio</a></li>
                    <!-- <li><a href="{{ URL::to('promociones') }}">Promociones</a></li> -->
				<li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
				<li><a href="{{ URL::to('explorar') }}">Explorar</a></li>
				<li class="divider"></li>
				<li><a href="{{ URL::to('cart') }}">Carrito</a></li>
				<li><a href="{{ URL::to('profile') }}">Mi perfil</a></li>
                    <li class="divider"></li>
                    @include('../include/linckChat')
                    <li class="divider"></li>
				@if(Auth::check())
					<li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li>
				@else
					<li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
				@endif
				</ul>
			</li>
			<!--<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>-->
			<li><a href="{{ URL::to('cart') }}"><i class="fa fa-shopping-cart fa-lg"></i> Carrito</a></li>
    </ul>
    <form class="navbar-form navbar-left search-bar" role="search">
		<div class="form-group">
			<div class="input-group">
				<label for="tags" class="input-group-addon red-label">
					<i class="fa fa-search"></i>
				</label>
			  	<input type="text" id="tags" name="tags" class="form-control searchTags" placeholder="Search">
			</div>
		</div>
	</form>
    <ul class="nav navbar-nav navbar-right">
		@if(Auth::check())
			<li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
            <!-- <li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li>-->
            @include('../include/linckChat')
		@else
			<li style="margin-right:50px;"><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
			<li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
		@endif
    </ul>
  </div>
</nav>
<div class="container below_bar white_content">
	<div class="center_content">
		@if(!empty($addresses) && count($addresses) > 0)		
			<div class="address_container space">
				<div class="address">
					<h1>Mis direcciones.</h1>
					<p>Agrega una nueva dirección o administra las que ya tienes.</p>
					@foreach(array_chunk($address, 3) as $key=>$value)
						<div class="row space">
							@foreach($value as $ke=>$val)
								<?php $myAddress = $val ?>
								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
									<h3>{{ $val->address_name. ' ' }} <button onClick="edit('{{ $val->address_id }}')" class="btn btn-success" data-toggle="modal" data-target="#address"><i class="fa fa-pencil-square-o fa-lg"></i></button></h3>
									<table class="table space" style="text-align:left;">
										<tr>
											<td>Dirección: {{$val->address_1}}</td>
										</tr>
										<tr>
											<td>Referencia: {{$val->reference}}</td>
										</tr>
										<tr>
											<td>Zona: {{$val->zone->zone}}</td>
										</tr>
									</table>
									{{ Form::open(array('url'=>'user/address/'.$val->address_id, 'onsubmit'=>'return confirm("Eliminar la dirección?");')) }}
										{{ Form::hidden('_method', 'DELETE' ) }} 
										{{ Form::submit('Eliminar', array('class'=>'btn btn-default button_150 space')) }}
									{{ Form::close() }}
								</div>
								<script type="text/javascript">
									$("#update_{{ $val->address_id }}").click(function(){
										$(this).removeClass("btn-success");
										$(this).addClass("btn-primary");
										$("input").removeAttr("disabled");
										$("#addrss_updt_{{ $val->address_id }}").removeAttr("style");
										$("#msg-update_{{ $val->address_id }}").html("Puedes comenzar a editar tu dirección");
									});
								</script>
							@endforeach	
						</div>
					@endforeach
				</div>
				
			</div>
                
		<button type="button" class="btn btn-primary space_50" id="new-address" data-toggle="modal" data-target="#address">Nueva dirección</button>
				<div class="modal fade" id="address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		{{ Form::open(array('url' => 'user/address/create', 'onsubmit'=>'return enviar()', 'method' => 'POST', 'class' => 'new-address', 'id' => 'address-form')) }}
		<div class="modal-dialog" style="color: #3a3a3a; text-align:left;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4>Nueva dirección</h4>
				</div>
				<div class="modal-body" >
					<p>Completa el siguiente formulario para agregar una nueva dirección</p>
					<p class="bullet">Campos marcados con * son obligatorios</p>
					<br>
					<div class="form-group new_add_modal">
						{{ Form::text('address_name', null, array('id'=>'address_name','placeholder' => '* Nombre de registro ej. Mi casa', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					{{ Form::text('address_1', null, array('id'=>'address_1','placeholder' => '* Dirección', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					{{ Form::text('address_2', null, array('id'=>'address_2','placeholder' => 'Complemento de dirección', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
                                        {{ Form::text('reference', null, array('id'=>'reference','placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					{{ Form::select('state', $states, '0', array('id'=>'state','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
                                        {{ Form::select('municipality', $municipalities, NULL, array('id'=>'municipality','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
                                        {{ Form::select('zone_id', $zones, NULL, array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
					</div>
				</div>
				<div class="modal-footer" >
					{{ Form::submit('Guardar',  array('id'=>'btnSubmit','class'=>'btn btn-default button_150 space')) }}
				</div>
			</div>
		</div>
		{{ Form::close() }}
		@else
			<h1>No tiene registrada una dirección.</h1>
			<p>Complete el siguiente formulario para agregar una nueva:</p>
			<div class="half">
				{{ Form::open(array('url' => 'user/address/create', 'onsubmit'=>'return enviar()', 'method' => 'POST', 'class' => 'new-address', 'id' => 'address-form')) }}
					{{ Form::text('address_name', null, array('placeholder' => '* Nombre de registro ej. Mi casa', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					{{ Form::text('address_1', null, array('placeholder' => '* Dirección', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					{{ Form::text('address_2', null, array('placeholder' => 'Complemento de dirección', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
                                        {{ Form::text('reference', null, array('placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					{{ Form::select('state', $states, '0', array('id'=>'state','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
                                        {{ Form::select('municipality', $municipalities, NULL, array('id'=>'municipality','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
                                        {{ Form::select('zone_id', $zones, NULL, array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
					{{ Form::submit('Agregar dirección',  array('id'=>'btnSubmit','class'=>'btn btn-default button_150 space')) }}
				{{ Form::close() }}
			</div>
                        
                        
		@endif
	</div>
	</div>
</div>

<script>
    var municipality = '';
    var zone = '';
    var idEdit = 0;
    
    $(document).ready(function(){
        $("#state").change(function(){
            getMunicipalities($(this).val());
        });
        
        $("#municipality").change(function(){
            getZones($(this).val());
        });
        
        $('#address').on('hidden.bs.modal', function () {
            idEdit = 0;
            $("#address-form")[0].reset();
        });
    });
    
    function enviar(){
        var params = $("#address-form").serialize();
        if(idEdit!=0){
            params+='&address_id='+idEdit;
        }
            $.post((idEdit!=0)? '{{URL::to("user/address/edit")}}':'{{URL::to("user/address/create")}}', 
                params,
                function(data){
                    if(data.status){
                        $("#address").modal('hide');
                        location.reload();
                    }else{
                        var s;
                        $.each(data.data, function(i, item){
                            s = item;
                        });
                        
                        alert(s);
                    }
                }, 'json');
                
                return false;
    }
    
    function getMunicipalities(idState){
        $.post('{{URL::to("user/address/municipalities")}}'
            ,{'state_id':idState},
            function(data){
                if(data.status){
                    $("#municipality").html('');
                    $("#municipality").append($("<option/>", {'value':'', 'text':'--Seleccione un municipio--'}));
                    $.each(data.data, function(i, item){
                        $("#municipality").append($("<option/>", {'value':item.municipality_id, 'text':item.municipality}));
                    });
                    $("#municipality").val(municipality);
                    municipality='';
                    
                    
                    //Sino se edita
                    if(idEdit==0){
                        $("#zone_id").html('');
                        $("#zone_id").append($("<option/>", {'value':'', 'text':'--Seleccione una zona--'}));
                        $("#zone_id").val('');
                    }
                }else{
                    alert("Ocurrió un error al obtener los municipios del departamento");
                }
            }, 'json'
        );
    }
    
    function getZones(idMunicipality){
        $.post('{{URL::to("user/address/zonesByMunicipality")}}'
            ,{'municipality_id':idMunicipality},
            function(data){
                if(data.status){
                    $("#zone_id").html('');
                    
                    $("#zone_id").append($("<option/>", {'value':'', 'text':'--Seleccione una zona--'}));
                    
                    $.each(data.data, function(i, item){
                        $("#zone_id").append($("<option/>", {'value':item.zone_id, 'text':item.zone}));
                    });
                    
                    $("#zone_id").val(zone);
                    zone = '';
                }else{
                    alert("Ocurrió un error al obtener las zonas del municipio");
                }
            }, 'json'
        );
    }
    
    function edit(idAddress){
        idEdit=idAddress;
        $.get("{{URL::to('user/address/edit/')}}/"+idAddress,
        function(data){
            $("#address_name").val(data.address.address_name);
            $("#address_1").val(data.address.address_1);
            $("#address_2").val(data.address.address_2);
            $("#reference").val(data.address.reference);
            $("#state").val(data.municipality.state_id);
            
            municipality = data.municipality.municipality_id;
            zone = data.zone.zone_id;
            
            getMunicipalities(data.municipality.state_id);
            getZones(data.municipality.municipality_id);
        }, 'json');
    }
    
    
</script>
@stop