@if ($paginator->hasPages())
	<nav role="navigation" aria-label="Paginación" class="paginacion-nav">
		@if ($paginator->onFirstPage())
			<span class="paginacion-enlace paginacion-enlace--deshabilitado">&laquo; Anterior</span>
		@else
			<a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="paginacion-enlace">&laquo; Anterior</a>
		@endif

		<span class="paginacion-numeros">
			@foreach ($elements as $element)
				@if (is_string($element))
					<span class="paginacion-puntos">{{ $element }}</span>
				@endif

				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
							<span class="paginacion-pagina paginacion-pagina--actual" aria-current="page">{{ $page }}</span>
						@else
							<a href="{{ $url }}" class="paginacion-pagina">{{ $page }}</a>
						@endif
					@endforeach
				@endif
			@endforeach
		</span>

		@if ($paginator->hasMorePages())
			<a href="{{ $paginator->nextPageUrl() }}" rel="next" class="paginacion-enlace">Siguiente &raquo;</a>
		@else
			<span class="paginacion-enlace paginacion-enlace--deshabilitado">Siguiente &raquo;</span>
		@endif
	</nav>
@endif
