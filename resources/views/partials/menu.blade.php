<ul class="menu-lista">
	@foreach ($categoriasMenu as $item)
		<li>
			<a href="{{ route('categoria', $item->slug) }}" class="{{ request()->routeIs('categoria') && request()->route('categoria')?->slug === $item->slug ? 'activo' : '' }}">
				{{ $item->nombre }}
			</a>
		</li>
	@endforeach
</ul>
