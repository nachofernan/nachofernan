@if ($paginator->hasPages())
	<nav role="navigation" aria-label="Paginación" class="admin-paginacion">
		@if ($paginator->onFirstPage())
			<span class="admin-paginacion-enlace admin-paginacion-enlace--deshabilitado">&laquo; Anterior</span>
		@else
			<a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="admin-paginacion-enlace">&laquo; Anterior</a>
		@endif

		<span class="admin-paginacion-numeros">
			@foreach ($elements as $element)
				@if (is_string($element))
					<span class="admin-paginacion-puntos">{{ $element }}</span>
				@endif

				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
							<span class="admin-paginacion-pagina admin-paginacion-pagina--actual" aria-current="page">{{ $page }}</span>
						@else
							<a href="{{ $url }}" class="admin-paginacion-pagina">{{ $page }}</a>
						@endif
					@endforeach
				@endif
			@endforeach
		</span>

		@if ($paginator->hasMorePages())
			<a href="{{ $paginator->nextPageUrl() }}" rel="next" class="admin-paginacion-enlace">Siguiente &raquo;</a>
		@else
			<span class="admin-paginacion-enlace admin-paginacion-enlace--deshabilitado">Siguiente &raquo;</span>
		@endif
	</nav>
@endif
