@extends('layouts.admin')

@section('titulo', 'Entradas')

@section('contenido')
	<div class="admin-encabezado-entrada">
		<h1>Entradas</h1>
		<a href="{{ route('admin.entradas.create') }}" class="admin-boton">Nueva entrada</a>
	</div>

	<table class="admin-tabla">
		<thead>
			<tr>
				<th>Título</th>
				<th>Categoría</th>
				<th>Tipo</th>
				<th>Estado</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@forelse ($entradas as $entrada)
				<tr>
					<td><a href="{{ route('admin.entradas.edit', $entrada) }}">{{ $entrada->titulo }}</a></td>
					<td>{{ $entrada->categoria?->nombre }}</td>
					<td>{{ $entrada->tipo === 'cita' ? 'Cita' : 'Entrada' }}</td>
					<td>
						<span class="admin-estado admin-estado--{{ $entrada->estado }}">
							{{ $entrada->estado === 'publicada' ? 'Publicada' : 'Borrador' }}
						</span>
					</td>
					<td><a href="{{ route('admin.entradas.edit', $entrada) }}">Editar</a></td>
				</tr>
			@empty
				<tr><td colspan="5">Todavía no hay entradas.</td></tr>
			@endforelse
		</tbody>
	</table>

	<div class="admin-paginacion-envoltorio">
		{{ $entradas->links('pagination.admin') }}
	</div>
@endsection
