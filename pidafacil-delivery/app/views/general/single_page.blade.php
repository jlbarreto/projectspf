<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	
	<title>Dinamico</title>
	@include('include.head')
	{{ HTML::style('css/boobstrap/bootstrap.css') }}
	{{-- HTML::style('css/boobstrap/font-awesome.css') --}}
	{{ HTML::style('css/boobstrap/font-awesome.min.css') }}
	{{ HTML::style('css/news/general.css') }}
	<!--<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">-->
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
	</script>
	<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=1425798777733667&amp;ev=PixelInitialized" /></noscript>

</head>
<body>
	<main>
		<section class="module parallax parallax-2">
			@yield('fContent')
		</section>
	</main>
	<footer>
		<div class="container-fluid white_content" style="background-color: #1a1a1a; padding: 25px;">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
						<p style="color: #5f5f5f;">Acerca de nosotros</p>
						<ul>
							<a href="#"><li>Contacto</li></a>
							<a href="#"><li>Prensa</li></a>
							<a href="#"><li>Terminos y condiciones</li></a>
							<a href="#"><li>Privacidad</li></a>
						</ul>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
						<p style="color: #5f5f5f;">Si tienes dificultades en el sitio:</p>
						<ul>
							<a href="#"><li>Preguntas frecuentes</li></a>
							<a href="#"><li>Mapa del sitio</li></a>
                            <a href="#" id="CopyURL">Copiar URL</a>
						</ul>
					</div>
					<!--<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<a href="#" class="btn btn_fb btn-block">Estamos en Facebook</a>
						<a href="#" class="btn btn_go btn-block">Estamos en Google +</a>
					</div>-->
				</div>
			</div>
		</div>
	</footer>
{{ HTML::script('js/boobstrap/bootstrap.js') }}
<script>
    $("#CopyURL").click(function(e){
        e.preventDefault();
                prompt('Ctrl+c Para copiar URL', '{{Request::url()}}');
    });

</script>
</body>
</html>	 