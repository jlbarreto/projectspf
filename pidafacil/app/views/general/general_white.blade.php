<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>PidaFacil</title>
		@include('include.head')
		{{ HTML::style('css/boobstrap/bootstrap.css') }}
		{{-- HTML::style('css/boobstrap/font-awesome.css') --}}
		{{ HTML::style('css/boobstrap/font-awesome.min.css') }}
		{{ HTML::style('css/news/general.css') }}
		{{ HTML::style('css/details-shim.css') }}
		{{ HTML::style('css/cart.css') }}
		{{ HTML::style('css/slick.css') }}
		{{ HTML::style('css/slick-theme.css') }}

		<style type="text/css">
			/* Hide AddToAny vertical share bar when screen is less than 980 pixels wide */
			@media screen and (max-width: 980px) {
			    .a2a_floating_style.a2a_vertical_style { display: none; }
			}
		</style>

		<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->
		<style>
			#logobanner{
	        	width: 65px;
	        	float: left;
	        	display: inline-block;
	        }

	        #bannerAndroid{
	        	background-color:white;
	        	height:75px;
	        	color:black;
	        	padding: 5px
	        }

	        #banneriOS{
	        	background-color:white;
	        	height:75px;
	        	color:black;        	
	        	padding: 5px
	        }

	        #botonDescarga{
	        	background-color:#032992; 
	        	border-color:#032992; 
	        	color:white;
	        }

	        #infoAndroid{
	        	float: left;
			    padding-left: 5px;
			    display: inline-block;
			    padding-top: 20px;
	        }

	        #infoIOS{
	        	float: left;
			    padding-left: 5px;
			    display: inline-block;
			    padding-top: 20px;
	        }

	        #botonA{
	        	float: left;
			    display: inline-block;
			    padding-left: 6px;
			    padding-top: 15px;
	        }

	        #botonI{
	        	float: left;
			    display: inline-block;
			    padding-left: 6px;
			    padding-top: 15px;
	        }

	    </style>
		<script>(function() {
		var _fbq = window._fbq || (window._fbq = []);
		
		if (!_fbq.loaded) {
		
		var fbds = document.createElement('script');
		fbds.async = true;
		fbds.src = '//connect.facebook.net/en_US/fbds.js';
		
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(fbds, s);
		_fbq.loaded = true;
		}
		_fbq.push(['addPixelId', '1425798777733667']);
		})();
		
		window._fbq = window._fbq || [];
		window._fbq.push(['track', 'PixelInitialized', {}]);

		$(document).ready(function(){
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
					$("#banneriOS").show();
					$("#bannerAndroid").hide();
				}else if(isMobile.Android()){
					$("#bannerAndroid").show();
					$("#banneriOS").hide();
				}
		  	}

		  	$('#cerrarBA').click(function(){
		    	$("#bannerAndroid").hide();
		   	});

		   	$('#cerrarBI').click(function(){
		    	$("#banneriOS").hide();
		   	});
		});

		</script>
		<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=1425798777733667&amp;ev=PixelInitialized" /></noscript>

		{{ HTML::script('js/jquery.creditCardValidator.js', array("type" => "text/javascript")) }}
		<script async src="https://static.addtoany.com/menu/page.js"></script>
	</head>
	<body>@include('../include/analyticstracking')
		<main>
			<section class="module parallax parallax-2">
				<div class="row">
					<div class="col-xs-12">
						<div id="bannerAndroid" style="display:none;">
							<span id="cerrarBA" style="width: 20px; display: inline-block; float: left; padding-top: 5%; font-size: 18px; color: gray; font-weight: bold;">X</span>
							{{ HTML::image('http://images.pf.techmov.co/pidafacil_icon.png', "Imagen no encontrada", array('id' => 'logobanner')) }}
							<div id="infoAndroid">
								<p style="font-size: 16px; display:inline-block">PidaFacil para Android</p>
							</div>
							<div id="botonA">
								<a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil" id="botonDescarga" class="btn btn-pimary">Descargar</a>							
							</div>						
						</div>
						<div id="banneriOS" style="display:none;">
							<span id="cerrarBI" style="width: 15px; display: inline-block; float: left; padding-top: 5%; font-size: 16px; color: gray; font-weight: bold;">X</span>
							{{ HTML::image('http://images.pf.techmov.co/pidafacil_icon.png', "Imagen no encontrada", array('id' => 'logobanner')) }}					
							<div id="infoIOS">
								<p style="font-size: 15px; display:inline-block">PidaFacil para iOS</p>
							</div>
							<div id="botonI">
								<a href="https://itunes.apple.com/us/app/id990772385" id="botonDescarga" class="btn btn-pimary">Descargar</a>
							</div>
						</div>
					</div>
				</div>
				@yield('content')
				<!-- Go to www.addthis.com/dashboard to customize your tools -->
				<!--Barra lateral para compartir en redes sociales-->
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-586d1b15c6f2888c"></script>

			</section>
		</main>
	    @include('../include/footer')
	</body>
</html>