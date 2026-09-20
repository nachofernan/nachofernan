@extends('layouts.blog')

@section('titulo', $entrada->titulo ? $entrada->titulo.' — '.config('app.name') : config('app.name'))

@section('contenido')

	<article class="entrada entrada-individual entrada-{{ $entrada->tipo }}">

		<div class="entrada-cuerpo">

			@unless ($entrada->esCita())
				<h1 class="entrada-titulo">{{ $entrada->titulo }}</h1>
			@endunless

			<div class="entrada-contenido">
				{!! $entrada->contenido !!}
			</div>

		</div>

	</article>

@endsection
