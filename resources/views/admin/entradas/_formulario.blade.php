@push('estilos')
	<link rel="stylesheet" href="{{ asset('vendor/trix/trix.css') }}">
@endpush

<form method="POST" action="{{ $accion }}" enctype="multipart/form-data" class="admin-form">
	@csrf
	@if ($entrada->exists)
		@method('PUT')
	@endif

	<label for="titulo">Título</label>
	<input type="text" id="titulo" name="titulo" value="{{ old('titulo', $entrada->titulo) }}" required autofocus>
	@error('titulo') <p class="admin-error">{{ $message }}</p> @enderror

	<div class="admin-form-fila">
		<div>
			<label for="categoria_id">Categoría</label>
			<select id="categoria_id" name="categoria_id" required>
				@foreach ($categorias as $categoria)
					<option value="{{ $categoria->id }}" @selected(old('categoria_id', $entrada->categoria_id) == $categoria->id)>
						{{ $categoria->nombre }}
					</option>
				@endforeach
			</select>
		</div>

		<div>
			<label for="tipo">Tipo</label>
			<select id="tipo" name="tipo" required>
				<option value="entrada" @selected(old('tipo', $entrada->tipo) === 'entrada')>Entrada</option>
				<option value="cita" @selected(old('tipo', $entrada->tipo) === 'cita')>Cita</option>
			</select>
		</div>

		<div>
			<label for="estado">Estado</label>
			<select id="estado" name="estado" required>
				<option value="borrador" @selected(old('estado', $entrada->estado) === 'borrador')>Borrador</option>
				<option value="publicada" @selected(old('estado', $entrada->estado) === 'publicada')>Publicada</option>
			</select>
		</div>
	</div>
	@error('categoria_id') <p class="admin-error">{{ $message }}</p> @enderror

	<label for="portada">Portada</label>
	@if ($entrada->portada)
		<img src="{{ asset('storage/'.$entrada->portada) }}" alt="" class="admin-portada-actual">
	@endif
	<input type="file" id="portada" name="portada" accept="image/*">
	@error('portada') <p class="admin-error">{{ $message }}</p> @enderror

	<label for="contenido-editor">Contenido</label>
	<input type="hidden" id="contenido" name="contenido" value="{{ old('contenido', $entrada->contenido) }}">
	<trix-editor input="contenido" id="contenido-editor" class="admin-trix"></trix-editor>
	@error('contenido') <p class="admin-error">{{ $message }}</p> @enderror

	<button type="submit">Guardar</button>
</form>

@push('scripts')
	<script src="{{ asset('vendor/trix/trix.js') }}"></script>
@endpush
