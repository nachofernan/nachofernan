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
<body class="admin @yield('modo')">

	<header class="admin-cabecera">
		<div class="admin-contenedor @yield('ancho')">
			<a href="{{ route('admin.entradas.index') }}" class="admin-marca">{{ config('app.name') }} <span>· admin</span></a>
			<nav class="admin-nav">
				<a href="{{ route('admin.entradas.index') }}">Entradas</a>
				<a href="{{ route('inicio') }}" target="_blank" rel="noopener">Ver el blog ↗</a>
				<form method="POST" action="{{ route('admin.logout') }}">
					@csrf
					<button type="submit">Cerrar sesión</button>
				</form>
			</nav>
		</div>
	</header>

	@hasSection('completo')
		<main class="admin-contenido admin-contenido--completo">
			@yield('completo')
		</main>
	@else
		<main class="admin-contenido">
			<div class="admin-contenedor @yield('ancho')">
				@if (session('estado'))
					<p class="admin-aviso">{{ session('estado') }}</p>
				@endif

				@yield('contenido')
			</div>
		</main>
	@endif

	@stack('scripts')
</body>
</html>
