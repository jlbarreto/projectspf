<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>jQuery UI Autocomplete - Default functionality</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    var data = [

			@foreach($autos as $auto)

			 { label :	"{{$auto['label']}}", value: "{{$auto['value']}}", type: "{{$auto['type']}}"},

			@endforeach

			];
     $( "#tags" ).autocomplete({
	          source: function( request, response ) {
	               var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
	             response( $.grep( data, function( item ){
	                 return matcher.test( item.label );
	             }) );
			    },
		    minLength: 1,
		    select: function(event, ui) {
		       event.preventDefault();
		       $("#tags").val(ui.item.label);
		       $("#selected-tag").val(ui.item.label);
		       if(ui.item.type == "slug"){
		       		window.location.href = "<?php echo URL::to('/'); ?>/" + ui.item.value;
		       }else{
		       		window.location.href = "<?php echo URL::to('/'); ?>/explorar/" + ui.item.label;

		       }

		    },
		   focus: function(event, ui) {
		       event.preventDefault();
		       $("#tags").val(ui.item.label);
		   }
		 });
  });
  </script>
</head>
<body>

<div class="ui-widget">
  <label for="tags">Tags: </label>


  <input id="tags"/>
</div>


</body>
</html>
