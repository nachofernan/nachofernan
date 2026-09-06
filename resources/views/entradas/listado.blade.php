@extends('layouts.blog')

@section('titulo', $tituloArchivo ? $tituloArchivo.' — '.config('app.name') : config('app.name'))

@section('contenido')

	@forelse ($entradas as $entrada)
		@include('entradas._tarjeta', ['entrada' => $entrada])
	@empty
		<article class="entrada">
			<div class="entrada-cuerpo">
				<p>No hay entradas para mostrar.</p>
			</div>
		</article>
	@endforelse

	<div class="paginacion">
		{{ $entradas->links('pagination.blog') }}
	</div>

@endsection
