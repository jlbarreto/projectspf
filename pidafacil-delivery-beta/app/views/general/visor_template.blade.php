<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
    @if(Auth::user()->role_id == 1)
	    <title>{{ $restaurantes[$id]['name'] }} - PidaFacil</title>
    @else
        <title>PidaFacil Delivery</title>
    @endif
	@include('include.head')
	{{ HTML::style('css/boobstrap/bootstrap.css') }}
	{{-- HTML::style('css/boobstrap/font-awesome.css') --}}
	{{ HTML::style('css/boobstrap/font-awesome.min.css') }}
	{{ HTML::style('css/news/general.css') }}
</head>
<body onload="startTime()">
	<main>
		<section>
			@yield('content')
		</section>
	</main>
{{ HTML::script('js/boobstrap/bootstrap.js') }}
</body>
</html>	 