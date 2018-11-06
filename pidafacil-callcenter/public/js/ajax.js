$(document).ready(function(){
	//var producto = localStorage.getItem("product_id");
	//var formulario = $('#frmAddPrd_'+producto);	
	
	$.fn.changePrice = function(frmId) {
		/*$("#" + frmId).submit(function( event ){
			event.preventDefault();
			alert($(this).attr('method'));
			*/
			$.ajax({
				url     : $("#" + frmId).attr('action'),
	            type    : $("#" + frmId).attr('method'),
	            data    : $("#" + frmId).serialize(),
	            success : function( data ) {
	            	var update_cart = data;
					//document.getElementById('price_' + update_cart.arr_key).innerHTML ='$ ' + update_cart.total_price;
					location.reload()
	            },
	            error   : function( xhr, err ) {
	            	alert('Error');     
	            }
			});
			/*return false;
		});*/
		return false;
	}

	$.fn.changePriceOld = function(frmId) {
		$("#" + frmId).submit(function( event ){
			event.preventDefault();
			alert($(this).attr('method'));
			$.ajax({
				url     : $(this).attr('action'),
	            type    : $(this).attr('method'),
	            data    : $(this).serialize(),
	            success : function( data ) {
	            	var update_cart = data;
					//document.getElementById('price_' + update_cart.arr_key).innerHTML ='$ ' + update_cart.total_price;
					location.reload()
	            },
	            error   : function( xhr, err ) {
	            	alert('Error');     
	            }
			});
			return false;
		});
		return false;
	}

	$.fn.update_address = function(frmId) {
		$("#" + frmId).submit(function( event ){
			event.preventDefault();
			$.ajax({
				url     : $(this).attr('action'),
	            type    : $(this).attr('method'),
	            data    : $(this).serialize(),
	            success : function( data ) {
	            	var json = data;
					location.reload();        	
	            },
	            error   : function( xhr, err ) {
	            	alert('Error');     
	            }
			});
			return false;
		});
		return false;
	}
	//console.log(formulario);
	

});
$(".addCart2").click(function(){
	var formulario = $('#frmAddPrd_'+producto);
	//console.log(formulario+'click');
	var producto = localStorage.getItem("product_id");

	$('#frmAddPrd_'+producto).validate({
	    highlight: function (element) {
	    	$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	    },
	    success: function (element) {
	        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
	    },
	    submitHandler: function(form) {
	    	
	    	//console.log('llega a ajax');
			$.ajax({
				url     : $(form).attr('action'),
	            type    : $(form).attr('method'),
	            data    : $(form).serialize(),
				success : function(dataCheck){
					var jsonData = dataCheck;					
					$('#add_'+producto).modal('show');
					if(typeof(jsonData.message_error) != "undefined" && jsonData.message_error !== null){
						document.getElementById("validate_"+producto).innerHTML = jsonData.message_error;
						var x = document.getElementById("validate_"+producto);
						x.style.color = "red";
						x.style.background = "#ffaeae";
						x.style.fontSize = "20px";
						x.style.borderRadius = "5px";
						x.style.padding = "15px";
					}

					if(typeof(jsonData.message_success) != "undefined" && jsonData.message_success !== null){
						document.getElementById("validate_"+producto).innerHTML = jsonData.message_success;
						var x = document.getElementById("validate_"+producto);
						x.style.color = "green";
						x.style.backgroundColor = "#d2ffd6";
						x.style.fontSize = "20px";
						x.style.borderRadius = "5px";
						x.style.padding = "15px";
					}
				},
	            error   : function( xhr, err ) {
	            	alert('Error');     
	            }
			});
		}
	});
});
