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

	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->

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

	{{ HTML::script('js/jquery.creditCardValidator.js', array("type" => "text/javascript")) }}
</head>
<body>@include('../include/analyticstracking')
	<main>
		<section class="module parallax parallax-2">
			@yield('content')
		</section>
	</main>
    @include('../include/footer')
</body>
</html>