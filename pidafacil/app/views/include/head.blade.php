{{HTML::style('css/parallax.css')}}
{{HTML::style('css/filters.css')}}
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="manifest" href="/manifest.json" />
{{ HTML::script('js/jquery-1.11.2.min.js', array("type" => "text/javascript")) }}
{{ HTML::script('js/slick.min.js', array("type" => "text/javascript")) }}
<!--<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
<?php
	//Si tiene como valor en type : tag_id es un tag si tiene un slug es un restaurante esto es para saber si redireccionar
	//hacia un restaurante o hacia un tag en específico
	if (Session::has('autocomplete')){
		$autos = Session::get('autocomplete');
	}else{
		$autos = array();
		$tags = DB::select('select tags.tag_id, tags.tag_name, tipos_tags.tag_type_id from com_tags as tags
			inner join com_tag_types as tipos_tags on tags.tag_type_id = tipos_tags.tag_type_id
			where tipos_tags.tag_type_id in (1, 2, 4)
			order by tipos_tags.tag_type_id');
		$restaurants = DB::select('select restaurante.restaurant_id, restaurante.name, restaurante.slug
			from res_restaurants restaurante
			where restaurante.parent_restaurant_id = restaurante.restaurant_id
			and restaurante.activate = 1');

		foreach($tags as $tag){
			$add = array('label' => $tag->tag_name,
						 'type'  => 'tag_id',
						 'value' => $tag->tag_id
						 );

			array_push($autos, $add);
		}
		foreach($restaurants as $restaurant){
			$add = array('label' => $restaurant->name,
						 'type'  => 'slug',
						 'value' => $restaurant->slug);
			array_push($autos, $add);
		}

		Session::put('autocomplete', $autos);
		$autos = Session::get('autocomplete');
	}
?>

	@if(isset($autos))
	{{ HTML::style('css/jquery-ui.css') }}
	{{ HTML::script('js/jquery-ui-1.11.4.min.js', array("type" => "text/javascript")) }}
	<script>
	$(function() {
		var data = [
			@foreach($autos as $auto){
				label: "{{ $auto['label'] }}",
				value: "{{ $auto['value'] }}",
				type: "{{ $auto['type'] }}"
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
		
		$("#tags").autocomplete({
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
				$("#tags").val(ui.item.label);
			   	$("#selected-tag").val(ui.item.label);
                appboy.logCustomEvent("Search", {'search_criteria':ui.item.label});
			    if(ui.item.type == "slug"){
			    	window.location.href = "<?php echo URL::to('/'); ?>/" + ui.item.value;
			    }else{
			    	window.location.href = "<?php echo URL::to('/'); ?>/explorar/" + ui.item.label;
			    }
			    return false;
			},
			focus: function(event, ui) {
				event.preventDefault();
			    $("#tags").val(ui.item.label);
			    return false;
			}
		});
	});
	</script>
	@endif
