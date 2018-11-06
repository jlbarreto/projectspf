$(document).ready(function(){
	/*
	$('#ajax').click(function(){
		$(".create_order").submit(function( event ){
			event.preventDefault();
			$.ajax({
				url     : $(this).attr('action'),
	            type    : $(this).attr('method'),
	            data    : $(this).serialize(),
				success : function(dataCheck){
					var jsonData = dataCheck;

					if(typeof(jsonData.message_error) != "undefined" && jsonData.message_error !== null){
						document.getElementById("validate").innerHTML = jsonData.message_error;
						var x = document.getElementById("validate");
						x.style.color = "red";
						x.style.background = "#ffaeae";
						x.style.fontSize = "20px";
						x.style.borderRadius = "5px";
						x.style.padding = "15px";
					}

					if(typeof(jsonData.message_success) != "undefined" && jsonData.message_success !== null){
						document.getElementById("validate").innerHTML = jsonData.message_success;
						var x = document.getElementById("validate");
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
			return false;
		});
	});
	*/
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

	$('#frmAddPrd').validate({
	    highlight: function (element) {
	    	$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	        
	    },
	    success: function (element) {
	        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
	    },
	    submitHandler: function(form) {
			$.ajax({
				url     : $(form).attr('action'),
	            type    : $(form).attr('method'),
	            data    : $(form).serialize(),
				success : function(dataCheck){
					var jsonData = dataCheck;
					$('#add').modal('show');
					if(typeof(jsonData.message_error) != "undefined" && jsonData.message_error !== null){
						document.getElementById("validate").innerHTML = jsonData.message_error;
						var x = document.getElementById("validate");
						x.style.color = "red";
						x.style.background = "#ffaeae";
						x.style.fontSize = "20px";
						x.style.borderRadius = "5px";
						x.style.padding = "15px";
					}

					if(typeof(jsonData.message_success) != "undefined" && jsonData.message_success !== null){
						document.getElementById("validate").innerHTML = jsonData.message_success;
						var x = document.getElementById("validate");
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
