@extends('layouts.admin')

@section('titulo', 'Nueva entrada')
@section('modo', 'admin--editor')

@section('completo')
	@include('admin.entradas._formulario', [
		'entrada' => new App\Models\Entrada(),
		'accion' => route('admin.entradas.store'),
	])
@endsection
