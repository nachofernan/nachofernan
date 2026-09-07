@extends('layouts.admin')

@section('titulo', 'Editar: '.$entrada->titulo)
@section('modo', 'admin--editor')

@section('completo')
	@include('admin.entradas._formulario', [
		'entrada' => $entrada,
		'accion' => route('admin.entradas.update', $entrada),
	])
@endsection
