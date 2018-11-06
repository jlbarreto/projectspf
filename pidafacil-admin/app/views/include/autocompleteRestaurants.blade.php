{{HTML::style('css/parallax.css')}}
{{HTML::style('css/filters.css')}}
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">

{{ HTML::script('js/jquery-1.11.2.min.js', array("type" => "text/javascript")) }}
<!--<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
<?php
	//Si tiene como valor en type : tag_id es un tag si tiene un slug es un restaurante esto es para saber si redireccionar
	//hacia un restaurante o hacia un tag en específico
//Session::forget('autocompleteRestaurants');
	
		$autos = array();
                $autosAll = array();
		$restaurants = Restaurant::all();
		
		foreach($restaurants as $restaurant){
			$add = array('label' => $restaurant->name, 
						 'id'  => $restaurant->restaurant_id,
						 'value' => $restaurant->slug);
			array_push($autosAll, $add);
                        
                        if($restaurant->parent_restaurant_id==$restaurant->restaurant_id){
                            array_push($autos, $add);
                        }
		}
		
?>

	@if(isset($autos))
	{{ HTML::style('css/jquery-ui.css') }}
	{{ HTML::script('js/jquery-ui-1.11.4.min.js', array("type" => "text/javascript")) }}
	<script>
	$(function() {
            
            $( document ).tooltip();
            
		var dataAll = [
			@foreach($autosAll as $auto)
			{ 
				label: "{{ $auto['label'] }}", 
				value: "{{ $auto['value'] }}", 
				id: "{{ $auto['id'] }}"
			},
			@endforeach
		];
            
		var data = [
			@foreach($autos as $auto)
                                { 
                                    label: "{{ $auto['label'] }}", 
                                    value: "{{ $auto['value'] }}", 
                                    id: "{{ $auto['id'] }}"
                                },
			@endforeach
		];
		var accentMap = {
			"á": "a",
			"í": "i"
		};
		var normalize = function( term ) {
			var ret = "";
			for ( var i = 0; i < term.length; i++ ) {
				ret += accentMap[ term.charAt(i) ] || term.charAt(i);
			}
			return ret;
		};
                
                //TODO: Aqui hay que ver como el usuario puede pelarse y meter un dato luego borrarlo o editarlo
                //Erase value if delete input
                $("#padre, #procesaOrdenes").keyup(function(e){
                    var code = e.keyCode || e.which;
                    var hijo = findByPadre(this);
                    if(code===8){
                        hijo.val(0);
                    }
                });
                
		$("#padre").autocomplete({
			source: function( request, response ) { 
		    	var matcher = new RegExp( "\\b" + $.ui.autocomplete.escapeRegex( request.term ), 'i' );
		    	response( $.grep( data, function( item ){
		    		value = item.label || item.value || item;
		    		return matcher.test( value ) || matcher.test( normalize( value ) ); 
		        	/*return matcher.test( item.label );*/
		        }));
			},
			minLength: 1,
			select: function(event, ui) {
				event.preventDefault();
				$(this).val(ui.item.label);
			   	findByPadre(this).val(ui.item.id);
			    
			    return false;
			},
			focus: function(event, ui) {
				event.preventDefault();
			    $(this).val(ui.item.label);
                            findByPadre(this).val(ui.item.id);
			    return false;
			}
		});
                
		$("#procesaOrdenes").autocomplete({
			source: function( request, response ) { 
		    	var matcher = new RegExp( "\\b" + $.ui.autocomplete.escapeRegex( request.term ), 'i' );
		    	response( $.grep( dataAll, function( item ){
		    		value = item.label || item.value || item;
		    		return matcher.test( value ) || matcher.test( normalize( value ) ); 
		        	/*return matcher.test( item.label );*/
		        }));
			},
			minLength: 1,
			select: function(event, ui) {
				event.preventDefault();
				$(this).val(ui.item.label);
			   	findByPadre(this).val(ui.item.id);
			    
			    return false;
			},
			focus: function(event, ui) {
				event.preventDefault();
			    $(this).val(ui.item.label);
                            findByPadre(this).val(ui.item.id);
			    return false;
			}
		});
	});
        
        function findByPadre(trigger){
            if(trigger.id=='padre'){
                return $("input[name='parent_restaurant_id']");
            }else{
                return $("input[name='orders_allocator_id']");
            }
        }
	</script>
	@endif