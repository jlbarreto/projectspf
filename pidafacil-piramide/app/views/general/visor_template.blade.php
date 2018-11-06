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
</head>
<body onload="startTime()">
@include('../include/analyticstracking')
	<main>
		<section>
			@yield('content')
		</section>
	</main>
{{ HTML::script('js/boobstrap/bootstrap.js') }}
</body>
</html>	 