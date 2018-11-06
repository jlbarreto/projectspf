<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
        <title>PidaFacil Delivery</title>
	@include('include.head')
	{{ HTML::style('css/boobstrap/bootstrap.css') }}
	{{-- HTML::style('css/boobstrap/font-awesome.css') --}}
	{{ HTML::style('css/boobstrap/font-awesome.min.css') }}
	{{ HTML::style('css/news/general.css') }}
	{{ HTML::style('css/app.css') }}
</head>
<body onload="startTime()">
	<main>
		<section>
			@yield('content')
		</section>
	</main>
{{ HTML::script('js/boobstrap/bootstrap.js') }}
{{HTML::script('http://api.html5media.info/1.1.5/html5media.min.js')}}
</body>
</html>
