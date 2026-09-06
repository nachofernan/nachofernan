<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Entrada;
use Illuminate\View\View;

class EntradaController extends Controller
{
    public function index(): View
    {
        $entradas = Entrada::publicadas()
            ->conCategoria()
            ->recientesPrimero()
            ->paginate(10);

        return view('entradas.listado', [
            'entradas' => $entradas,
            'tituloArchivo' => null,
        ]);
    }

    public function porCategoria(Categoria $categoria): View
    {
        $entradas = $categoria->entradas()
            ->publicadas()
            ->conCategoria()
            ->recientesPrimero()
            ->paginate(10);

        return view('entradas.listado', [
            'entradas' => $entradas,
            'tituloArchivo' => $categoria->nombre,
        ]);
    }

    public function mostrar(string $slug): View
    {
        $entrada = Entrada::publicadas()
            ->conCategoria()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('entradas.mostrar', [
            'entrada' => $entrada,
            'imagenCabecera' => $entrada->portada ? asset('storage/'.$entrada->portada) : null,
        ]);
    }
}
