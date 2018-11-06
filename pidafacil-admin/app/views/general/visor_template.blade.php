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