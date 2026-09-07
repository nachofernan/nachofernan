@extends('layouts.admin')

@section('titulo', 'Entradas')

@section('contenido')
	<div class="admin-barra">
		<div>
			<h1>Entradas</h1>
			<p class="admin-barra-datos">
				{{ $entradas->total() }} {{ $entradas->total() === 1 ? 'entrada' : 'entradas' }} en total
			</p>
		</div>
		<div class="admin-barra-acciones">
			<a href="{{ route('admin.entradas.create') }}" class="admin-boton">Nueva entrada</a>
		</div>
	</div>

	<div class="admin-tabla-envoltorio">
		<table class="admin-tabla">
			<thead>
				<tr>
					<th class="admin-tabla-portada"></th>
					<th>Título</th>
					<th>Categoría</th>
					<th>Tipo</th>
					<th>Estado</th>
					<th>Fecha</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@forelse ($entradas as $entrada)
					<tr>
						<td class="admin-tabla-portada">
							@if ($entrada->portada)
								<img src="{{ asset('storage/'.$entrada->portada) }}" alt="" class="admin-miniatura">
							@else
								<span class="admin-miniatura admin-miniatura--vacia"></span>
							@endif
						</td>
						<td>
							<a href="{{ route('admin.entradas.edit', $entrada) }}" class="admin-tabla-titulo">{{ $entrada->titulo }}</a>
							<span class="admin-tabla-slug">/{{ $entrada->slug }}</span>
						</td>
						<td class="admin-tabla-secundario">{{ $entrada->categoria?->nombre ?? '—' }}</td>
						<td>
							<span class="admin-etiqueta {{ $entrada->esCita() ? 'admin-etiqueta--cita' : '' }}">
								{{ $entrada->esCita() ? 'Cita' : 'Entrada' }}
							</span>
						</td>
						<td>
							<span class="admin-estado admin-estado--{{ $entrada->estado }}">
								{{ $entrada->estaPublicada() ? 'Publicada' : 'Borrador' }}
							</span>
						</td>
						<td class="admin-tabla-fecha">{{ $entrada->publicada_en?->format('d/m/Y') ?? '—' }}</td>
						<td class="admin-tabla-acciones">
							<a href="{{ route('admin.entradas.edit', $entrada) }}" class="admin-accion">Editar</a>
							@if ($entrada->estaPublicada())
								<a href="{{ route('entrada', $entrada->slug) }}" target="_blank" rel="noopener" class="admin-accion">Ver</a>
							@endif
						</td>
					</tr>
				@empty
					<tr><td colspan="7" class="admin-vacio">Todavía no hay entradas.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>

	<div class="admin-paginacion-envoltorio">
		{{ $entradas->links('pagination.admin') }}
	</div>
@endsection
