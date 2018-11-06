@extends('general.useraddress_page')
@section('fContent')
<?php 
    $i=0;
    $cart = Session::get('cart');
    $cantidad = Session::get('cart2');
?>
@foreach($addresses as $key => $value)
	@if(empty($value))<?php continue; ?>
	@else
		<?php $address[] = $value ?>
	@endif
@endforeach
<style type="text/css">
    #map{
        height: 300px;
        width: 500px;
        border:1px solid;
        color:black;
    }

    .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    .pac-container {
        font-family: Roboto;
    }

    #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
    }

    #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
    }
</style>

<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlW4UtDGbq8T5W3RkahGAAh6mtlsOf0_Q&signed_in=true&libraries=geometry"
        async defer></script>-->
<input type="hidden" id="cont" value="{{$cantidad}}">
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
    			<li><a href="{{ URL::to('explorar') }}">Explorar</a></li>
                <li id="descApp1" style="display:none;"><a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil">Descargar la App</a></li>
                <li id="descApp2" style="display:none;"><a href="https://itunes.apple.com/us/app/id990772385">Descargar la App</a></li>
                @if($promociones >= 1)<li><a href="{{ URL::to('promociones') }}">Promociones</a></li>@endif
    			<li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
    			<li class="divider"></li>
    			<li><a href="{{ URL::to('cart') }}" onclick="procesar()">Carrito de Compras</a></li>
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
    		<li><a href="{{ URL::to('cart') }}" onclick="procesar()"><span id="iconoContador">{{$cantidad}}&nbsp;</span><i class="fa fa-shopping-cart fa-lg"></i> Carrito de Compras</a></li>
        </ul>
        <form class="navbar-form navbar-left search-bar" role="search">
    		<div class="form-group">
    			<div class="input-group">
    				<label for="tags" class="input-group-addon red-label">
    					<i class="fa fa-search"></i>
    				</label>
    			  	<input type="text" id="tags" name="tags" class="form-control searchTags" placeholder="Buscar">
    			</div>
    		</div>
    	</form>
        <ul class="nav navbar-nav navbar-right">
    		@if(Auth::check())
    			@if(Auth::user()->name == '')
                    <li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->email }} </a></li>
                @else
                    <li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
                @endif
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
									<h3>{{ $val->address_name. ' ' }} 
                                        <button onClick="edit('{{ $val->address_id }}')" id="edit_{{$val->address_id}}" class="btn btn-success">
                                            <i class="fa fa-pencil-square-o fa-lg"></i>
                                        </button>
                                    </h3>
									<table class="table space" style="text-align:left;">
										<tr>
											<td>Dirección: {{$val->address_1}}</td>
										</tr>
										<tr>
											<td>Referencia: {{$val->reference}}</td>
										</tr>
										<tr>
                                            @if($val->zone['zone'] != '')
                                                <td>Zona: {{$val->zone['zone']}}</td>
                                            @else
                                                <td>Zona: --</td>
                                            @endif
										</tr>
									</table>
									<?php
                                        $id_direccion = $val->address_id;
                                        $direccion = DB::select('select EXISTS(select * FROM pf.req_orders WHERE address_id = "'.$id_direccion.'") as name
                                        ');
                                        $result = json_decode(json_encode($direccion), true);
                                        foreach ($result as $key => $value){
                                            $total = $value['name'];
                                        }
                                    ?>
                                    @if($total != 0 )
                                        <button class="btn btn-default button_150 space" id="not_delete_{{$id_direccion}}" onClick="noDelete('{{ $val->address_id }}')">Eliminar</button>
                                    @else
                                        {{ Form::open(array('url'=>'user/address/'.$val->address_id, 'onsubmit'=>'return confirm("Eliminar la dirección?");')) }}
                                            {{ Form::hidden('_method', 'DELETE' ) }} 
                                            {{ Form::submit('Eliminar', array('class'=>'btn btn-default button_150 space')) }}
                                        {{ Form::close() }}
                                    @endif
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
	        <button type="button" class="btn btn-primary space_50" id="new-address">Nueva dirección</button>
            <div class="modal fade" id="address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        		{{ Form::open(array('url' => 'user/address/create', 'onsubmit'=>'return enviar()', 'method' => 'POST', 'class' => 'new-address', 'id' => 'address-form')) }}
            		<div class="modal-dialog" style="color: #3a3a3a; text-align:left;">
            			<div class="modal-content">
            				<div class="modal-header">
            					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            					<h4>Nueva dirección</h4>
            				</div>
            				<div class="modal-body">
                                <div id="add1">
                                    <p>Completa el siguiente formulario para agregar una nueva dirección</p>
                                    <p class="bullet">Campos marcados con * son obligatorios</p>
                                    <div class="form-group new_add_modal">
                                        {{ Form::text('address_name', null, array('id'=>'address_name','placeholder' => '* Nombre de registro ej. Mi casa', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
                                        {{ Form::text('address_1', null, array('id'=>'address_1','placeholder' => '* Dirección', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
                                        <!--{{ Form::text('address_2', null, array('id'=>'address_2','placeholder' => 'Complemento de dirección', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}-->
                                        {{ Form::text('reference', null, array('id'=>'reference','placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
                                        <!--{{ Form::select('state', $states, '0', array('id'=>'state','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
                                        <!--{{ Form::select('municipality', $municipalities, NULL, array('id'=>'municipality','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
                                        <!--{{ Form::select('zone_id', $zones, NULL, array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
                                        {{Form::select('zone_id',$combobox, $selected,array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required','style'=>'text-align:left'))}}
                                        {{ Form::hidden('coordenadas', '', array('id' => 'coordenadas')) }}
                                        <p>&nbsp;</p>
                                        <span>Para un mejor servicio, ¿Desea compartir su ubicación?
                                            &nbsp;<input type="radio" name="location" value="si"> Si
                                            &nbsp;&nbsp;&nbsp;<input type="radio" name="location" value="no"> No
                                        </span>
                                    </div>
                                </div>
            				</div>
            				<div class="modal-footer" >
            					{{ Form::submit('Guardar', array('id'=>'btnSubmit','class'=>'btn btn-default button_150 space')) }}
            				</div>
            			</div>
            		</div>
        		{{ Form::close() }}
            </div>
		@else
			<h1>No tiene registrada una dirección.</h1>
			<p>Complete el siguiente formulario para agregar una nueva:</p>
			<div class="half">
				{{ Form::open(array('url' => 'user/address/create', 'onsubmit'=>'return enviar()', 'method' => 'POST', 'class' => 'new-address', 'id' => 'address-form')) }}
					{{ Form::text('address_name', null, array('placeholder' => '* Nombre de registro ej. Mi casa', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					{{ Form::text('address_1', null, array('placeholder' => '* Dirección', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					<!--{{ Form::text('address_2', null, array('placeholder' => 'Complemento de dirección', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}-->
                    {{ Form::text('reference', null, array('placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
					<!--{{ Form::select('state', $states, '0', array('id'=>'state','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
                    <!--{{ Form::select('municipality', $municipalities, NULL, array('id'=>'municipality','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
                    <!--{{ Form::select('zone_id', $zones, NULL, array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
                    {{Form::select('zone_id',$combobox, $selected,array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required','style'=>'text-align:left'))}}
					{{ Form::hidden('coordenadas', '', array('id' => 'coordenadas')) }}
                    <p>&nbsp;</p>
                    <span>Para un mejor servicio, ¿Desea compartir su ubicación?
                        &nbsp;Si {{ Form::radio('location', 'si', false) }}
                        &nbsp;&nbsp;&nbsp;No {{ Form::radio('location', 'no', false) }}
                    </span>

                    {{ Form::submit('Agregar dirección',  array('id'=>'btnSubmit','class'=>'btn btn-default button_150 space')) }}
				{{ Form::close() }}
			</div>
		@endif
    </div>
</div>

<div class="modal fade" id="mensaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Aviso</h4>
            </div>
            <div class="modal-body">
                <p>No se puede eliminar esta dirección.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mapa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Ubicación</h4>
            </div>
            <div class="modal-body">
                <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                <div id="map"></div>
                <input type="hidden" id="destination">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>

    $("#new-address").click(function(){
        $('#address').modal('show');
    });

    function procesar(){
        appboy.logCustomEvent("Ir al Carrito");
    }

    function mostrar_ubicacion(p){
        //alert('posición: '+p.coords.latitude+','+p.coords.longitude );
        var coords = localStorage.getItem('coords');
        $("#coordenadas").val(coords);
    }

    $("#btnSubmit").click(function(event){
        if($("#zone_id option:selected" ).val()== 'none'){
            event.preventDefault();
            alert("Debes elegir una zona.")
        }else{
        }

        if($("input:radio[name='location']").is(':checked')){
            //alert("seleccionó: " + $('input:radio[name=location]:checked').val());
            if($('input:radio[name=location]:checked').val() == 'si'){
                var coords = localStorage.getItem('coords');
                $("#coordenadas").val(coords);
            }else{
                $("#coordenadas").val('');
            }
        }else{
            event.preventDefault();
            alert("Debes elegir una opción sobre la ubicación.");
        }
    });

    $("input:radio[name=location]").click(function(){
        if($('input:radio[name=location]:checked').val() == 'si'){
            
            window.open('http://pidafacil.com/soporte/ubicacion_cliente', 'chat', 'top=10px,left=20px,width=600px,height=460px');
            /*$("#mapa").on('shown.bs.modal', function(){
                initialize();
            });*/
            /*$('#mapa').modal('show');
            $('#mapa').on('shown', function(){
                //initAutocomplete();                
            });*/
                        
        }else{
            $("#coordenadas").val('');
        }
    });

    var municipality = '';
    var zone = '';
    var idEdit = 0;

    var marker1, marker2;
    var map;
    var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var labelIndex = 0;
    var newArray;

    function initAutocomplete(){
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 13.7064502, lng: -89.2475361},
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function(){
            searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        // [START region_getplaces]
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function(){
            var places = searchBox.getPlaces();
            if(places.length == 0){
                return;
            }

            // Clear out the old markers.
            markers.forEach(function(marker){
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place){
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                markers.push(new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                }));

                if(place.geometry.viewport){
                    //Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                }else{
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
        // [END region_getplaces]
    }
    /*TERMINA BUSCADOR MAPS*/

    /*function initialize(){

        if(typeof navigator.geolocation == 'object'){
            var datos = navigator.geolocation.getCurrentPosition(mostrar_ubicacion);
        }

        navigator.geolocation.getCurrentPosition(function(position){
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
        
            var bangalore = { lat: position.coords.latitude, lng: position.coords.longitude};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: pos
            });

            // This event listener calls addMarker() when the map is clicked.
            /*google.maps.event.addListener(map, 'click', function(event) {
                addMarker(event.latLng, map);
            });*/

            // Add a marker at the center of the map.
            /*marker1 = new google.maps.Marker({
                position: {lat: position.coords.latitude, lng: position.coords.longitude},
                draggable: true,
                label: labels[labelIndex++ % labels.length],
                map: map
            });

            var bounds = new google.maps.LatLngBounds(
                marker1.getPosition());
            map.fitBounds(bounds);

            google.maps.event.addListener(marker1, 'position_changed', update);

            update();
        });
    }*/

    function update() {
        var path = [marker1.getPosition()];
        //var heading = google.maps.geometry.spherical.computeHeading(path);
        document.getElementById('destination').value = path.toString();
        document.getElementById('coordenadas').value = path.toString();
    }

    $(document).ready(function(){

        //$('#zone_id option[value="none"]').prop('selected', true);
        $('#zone_id option:contains("--Seleccione una zona")');
      
        var dispositivo = navigator.userAgent.toLowerCase();
        if( dispositivo.search(/iphone|ipod|ipad|android/) > -1 ){
            //alert("Esta navegando en un movil");

            var isMobile = {
                Android: function() {
                    return navigator.userAgent.match(/Android/i);
                },
                BlackBerry: function() {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function() {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                Opera: function() {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                Windows: function() {
                    return navigator.userAgent.match(/IEMobile/i);
                },
                any: function() {
                    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
                }
            };

            if(isMobile.iOS()){
                $("#descApp2").show();
                $("#descApp1").hide();
            }else if(isMobile.Android()){
                $("#descApp1").show();
                $("#descApp2").hide();
            }
        }

        if($("#cont").val() > 0){
            $("#iconoContador").show();
        }else{
            $("#iconoContador").hide();
        }        
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
    
    /*function getZones(idMunicipality){
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
    }*/
    
    function edit(idAddress){
        idEdit=idAddress;
        $.get("{{URL::to('user/address/edit/')}}/"+idAddress,
        function(data){
            $("#address_name").val(data.address.address_name);
            $("#address_1").val(data.address.address_1);
            $("#address_2").val(data.address.address_2);
            $("#reference").val(data.address.reference);
            $("#state").val(data.municipality.state_id);
            $("#zone_id").val(data.zone.zone_id);
            $('#address').modal('show');
            municipality = data.municipality.municipality_id;
            zone = data.zone.zone_id;
            
            getMunicipalities(data.municipality.state_id);
            //getZones(data.municipality.municipality_id);
        }, 'json');
    }

    function noDelete(idAddress){
        $('#mensaje').modal('show');
    } 
    
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlW4UtDGbq8T5W3RkahGAAh6mtlsOf0_Q&libraries=places&callback=initAutocomplete"></script>
@stop