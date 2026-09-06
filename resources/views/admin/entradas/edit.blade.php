@extends('layouts.admin')

@section('titulo', 'Editar: '.$entrada->titulo)

@section('contenido')
	<div class="admin-encabezado-entrada">
		<h1>Editar entrada</h1>
		<form method="POST" action="{{ route('admin.entradas.destroy', $entrada) }}" onsubmit="return confirm('¿Borrar esta entrada? No se puede deshacer.');">
			@csrf
			@method('DELETE')
			<button type="submit" class="admin-boton-peligro">Borrar</button>
		</form>
	</div>

	@include('admin.entradas._formulario', [
		'entrada' => $entrada,
		'accion' => route('admin.entradas.update', $entrada),
	])
@endsection
