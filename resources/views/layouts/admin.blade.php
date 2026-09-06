<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('titulo', 'Admin') — {{ config('app.name') }}</title>
	<link rel="stylesheet" href="{{ asset('css/blog.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
	@stack('estilos')
</head>
<body class="admin">

	<header class="admin-cabecera">
		<div class="admin-contenedor">
			<a href="{{ route('admin.entradas.index') }}" class="admin-marca">{{ config('app.name') }} · admin</a>
			<nav class="admin-nav">
				<a href="{{ route('inicio') }}" target="_blank">Ver el blog</a>
				<form method="POST" action="{{ route('admin.logout') }}">
					@csrf
					<button type="submit">Cerrar sesión</button>
				</form>
			</nav>
		</div>
	</header>

	<main class="admin-contenido">
		<div class="admin-contenedor">
			@if (session('estado'))
				<p class="admin-aviso">{{ session('estado') }}</p>
			@endif

			@yield('contenido')
		</div>
	</main>

	@stack('scripts')
</body>
</html>
