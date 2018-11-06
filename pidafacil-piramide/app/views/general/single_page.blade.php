<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>PidaFacil</title>
	<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
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
  <meta name="google-site-verification" content="JgIt1KnBNYMIpO6IXRy7zlY1YqBe8Z0ZXpql2maCw0Q" />
</head>
<body>
@include('../include/analyticstracking')
	<main>
		<section class="module parallax parallax-2">
			@yield('fContent')
		</section>
	</main>
    @include('../include/footer')
</body>
</html>
