@push('estilos')
	<link rel="stylesheet" href="{{ asset('vendor/trix/trix.css') }}">
@endpush

<form method="POST" action="{{ $accion }}" enctype="multipart/form-data" class="admin-form admin-editor">
	@csrf
	@if ($entrada->exists)
		@method('PUT')
	@endif

	<div class="admin-editor-principal">
		<input type="hidden" id="contenido" name="contenido" value="{{ old('contenido', $entrada->contenido) }}">

		<div class="admin-editor-barra">
			<a href="{{ route('admin.entradas.index') }}" class="admin-enlace-volver">← Entradas</a>
			<trix-toolbar id="barra-contenido"></trix-toolbar>
		</div>

		<div class="admin-editor-lienzo">
			<div class="admin-editor-columna">
				@if (session('estado'))
					<p class="admin-aviso">{{ session('estado') }}</p>
				@endif

				<input type="text" id="titulo" name="titulo" class="admin-editor-titulo" aria-label="Título"
					value="{{ old('titulo', $entrada->titulo) }}"
					placeholder="Título de la entrada" autofocus>
				@error('titulo') <p class="admin-error">{{ $message }}</p> @enderror

				<trix-editor input="contenido" toolbar="barra-contenido" id="contenido-editor" class="admin-trix" placeholder="Escribí acá…"></trix-editor>
				@error('contenido') <p class="admin-error">{{ $message }}</p> @enderror
			</div>
		</div>
	</div>

	<aside class="admin-lateral">
		<div class="admin-lateral-seccion">
			<div class="admin-acciones-guardar">
				<button type="submit" class="admin-boton admin-boton--ancho">Guardar</button>
				@if ($entrada->exists && $entrada->estaPublicada())
					<a href="{{ route('entrada', $entrada->slug) }}" target="_blank" rel="noopener" class="admin-boton-secundario admin-boton--ancho">Ver en el blog ↗</a>
				@endif
			</div>
		</div>

		<div class="admin-lateral-seccion">
			<h2 class="admin-panel-titulo">Publicación</h2>

			<div class="admin-campo">
				<label for="estado">Estado</label>
				<select id="estado" name="estado" required>
					<option value="borrador" @selected(old('estado', $entrada->estado) === 'borrador')>Borrador</option>
					<option value="publicada" @selected(old('estado', $entrada->estado) === 'publicada')>Publicada</option>
				</select>
			</div>

			<div class="admin-campo">
				<label for="categoria_id">Categoría</label>
				<select id="categoria_id" name="categoria_id" required>
					@foreach ($categorias as $categoria)
						<option value="{{ $categoria->id }}" @selected(old('categoria_id', $entrada->categoria_id) == $categoria->id)>
							{{ $categoria->nombre }}
						</option>
					@endforeach
				</select>
				@error('categoria_id') <p class="admin-error">{{ $message }}</p> @enderror
			</div>

			<div class="admin-campo">
				<label for="tipo">Tipo</label>
				<select id="tipo" name="tipo" required>
					<option value="entrada" @selected(old('tipo', $entrada->tipo) === 'entrada')>Entrada</option>
					<option value="cita" @selected(old('tipo', $entrada->tipo) === 'cita')>Cita</option>
				</select>
			</div>

			@if ($entrada->exists && $entrada->publicada_en)
				<p class="admin-nota">Publicada el {{ $entrada->publicada_en->format('d/m/Y') }} · <code>/{{ $entrada->slug }}</code></p>
			@endif
		</div>

		<div class="admin-lateral-seccion">
			<h2 class="admin-panel-titulo">Portada</h2>

			@if ($entrada->portada)
				<img src="{{ asset('storage/'.$entrada->portada) }}" alt="" class="admin-portada-actual">
			@else
				<p class="admin-portada-vacia">Sin portada</p>
			@endif

			<div class="admin-campo">
				<label for="portada">{{ $entrada->portada ? 'Reemplazar imagen' : 'Subir imagen' }}</label>
				<input type="file" id="portada" name="portada" accept="image/*">
				@error('portada') <p class="admin-error">{{ $message }}</p> @enderror
			</div>
		</div>

		@if ($entrada->exists)
			<div class="admin-lateral-seccion">
				<button type="submit" form="admin-borrar-entrada" class="admin-boton-peligro admin-boton--ancho">Borrar entrada</button>
				<p class="admin-nota">Se borra también la portada. No se puede deshacer.</p>
			</div>
		@endif
	</aside>
</form>

@if ($entrada->exists)
	<form id="admin-borrar-entrada" method="POST" action="{{ route('admin.entradas.destroy', $entrada) }}"
		onsubmit="return confirm('¿Borrar esta entrada? No se puede deshacer.');" hidden>
		@csrf
		@method('DELETE')
	</form>
@endif

@push('scripts')
	<script src="{{ asset('vendor/trix/trix.js') }}"></script>
@endpush
