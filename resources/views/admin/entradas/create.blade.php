@extends('layouts.admin')

@section('titulo', 'Nueva entrada')

@section('contenido')
	<h1>Nueva entrada</h1>

	@include('admin.entradas._formulario', [
		'entrada' => new App\Models\Entrada(),
		'accion' => route('admin.entradas.store'),
	])
@endsection
