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
@include('../include/analyticstracking')
<?php
	if(isset($landingpage[0])){
		$logo = $landingpage[0]->logo;
		$banner = $landingpage[0]->banner;
		$header = $landingpage[0]->header;
	}else{
		$logo = $landingpage->logo;
		$banner = $landingpage->banner;
		$header = $landingpage->header;
	} ?>
	<!-- modul paralla restaurant / module parallax restaurant -->
	<main class="module parallax restaurant" style="background-image: url({{ URL::to(Config::get('app.url_images').$header) }});">
		<section>
			@yield('restaurant')
		</section>
		<section class="module content">
			@yield('content')
		</section>
	</main>
@include('../include/footer')
</body>
</html>
