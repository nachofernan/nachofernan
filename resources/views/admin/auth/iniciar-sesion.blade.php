@extends('layouts.admin-auth')

@section('titulo', 'Iniciar sesión — ' . config('app.name'))

@section('contenido')
	<div class="admin-login">
		<h1 class="admin-login-titulo">Iniciar sesión</h1>

		@if ($errors->any())
			<p class="admin-error">{{ $errors->first() }}</p>
		@endif

		<form method="POST" action="{{ route('admin.login') }}" class="admin-form">
			@csrf

			<label for="email">Email</label>
			<input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

			<label for="password">Contraseña</label>
			<input type="password" id="password" name="password" required>

			<label class="admin-checkbox">
				<input type="checkbox" name="recordar" value="1"> Recordarme
			</label>

			<button type="submit">Entrar</button>
		</form>
	</div>
@endsection
