<article class="entrada entrada-{{ $entrada->tipo }}">

	@if ($entrada->portada && ! $entrada->esCita())
		<figure class="entrada-portada">
			<a href="{{ route('entrada', $entrada->slug) }}">
				<img src="{{ asset('storage/'.$entrada->portada) }}" alt="{{ $entrada->titulo }}">
			</a>
		</figure>
	@endif

	<div class="entrada-cuerpo">

		@unless ($entrada->esCita())
			<h2 class="entrada-titulo">
				<a href="{{ route('entrada', $entrada->slug) }}">{{ $entrada->titulo }}</a>
			</h2>
		@endunless

		<div class="entrada-contenido">
			{!! $entrada->contenido !!}
		</div>

	</div>

</article>
