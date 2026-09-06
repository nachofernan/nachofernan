<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('titulo', config('app.name'))</title>
	<link rel="stylesheet" href="{{ asset('css/blog.css') }}">
</head>
<body>

	<header class="cabecera">
		<div class="contenedor-ancho">
			<h1 class="titulo-blog"><a href="{{ route('inicio') }}">{{ config('app.name') }}</a></h1>
			<p class="bajada-blog">{{ config('blog.bajada') }}</p>
		</div>
	</header>

	<nav class="navegacion">
		<div class="contenedor-ancho">
			@include('partials.menu')
		</div>
	</nav>

	<figure class="imagen-cabecera {{ isset($imagenCabecera) ? 'imagen-cabecera--propia' : '' }}" style="background-image: url('{{ $imagenCabecera ?? asset('images/cabecera-sitio.jpg') }}');">
		<img src="{{ $imagenCabecera ?? asset('images/cabecera-sitio.jpg') }}" alt="">
	</figure>

	<main class="contenido">
		@yield('contenido')
	</main>

	<footer class="pie">
		<div class="contenedor-ancho pie-interior">
			<p class="pie-marca">{{ config('app.name') }}</p>

			<ul class="pie-lista">
				<li><a href="#">Link 1</a></li>
				<li><a href="#">Link 2</a></li>
				<li><a href="#">Link 3</a></li>
			</ul>
		</div>
	</footer>

</body>
</html>
