<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Entrada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EntradaController extends Controller
{
    public function index(): View
    {
        $entradas = Entrada::conCategoria()
            ->recientesPrimero()
            ->paginate(20);

        return view('admin.entradas.index', ['entradas' => $entradas]);
    }

    public function create(): View
    {
        return view('admin.entradas.create', ['categorias' => Categoria::orderBy('nombre')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarDatos($request);
        $datos['slug'] = $this->generarSlug($datos['titulo']);
        $datos['publicada_en'] = now();

        if ($request->hasFile('portada')) {
            $datos['portada'] = $request->file('portada')->store('portadas', 'public');
        }

        $entrada = Entrada::create($datos);

        return redirect()->route('admin.entradas.edit', $entrada)->with('estado', 'Entrada creada.');
    }

    public function edit(Entrada $entrada): View
    {
        return view('admin.entradas.edit', [
            'entrada' => $entrada,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Entrada $entrada): RedirectResponse
    {
        $datos = $this->validarDatos($request);

        if ($request->hasFile('portada')) {
            if ($entrada->portada) {
                Storage::disk('public')->delete($entrada->portada);
            }

            $datos['portada'] = $request->file('portada')->store('portadas', 'public');
        }

        $entrada->update($datos);

        return redirect()->route('admin.entradas.edit', $entrada)->with('estado', 'Cambios guardados.');
    }

    public function destroy(Entrada $entrada): RedirectResponse
    {
        if ($entrada->portada) {
            Storage::disk('public')->delete($entrada->portada);
        }

        $entrada->delete();

        return redirect()->route('admin.entradas.index')->with('estado', 'Entrada borrada.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'tipo' => ['required', 'in:entrada,cita'],
            'estado' => ['required', 'in:borrador,publicada'],
            'contenido' => ['required', 'string'],
            'portada' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    private function generarSlug(string $titulo): string
    {
        $base = Str::slug($titulo);
        $slug = $base;
        $numero = 2;

        while (Entrada::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$numero}";
            $numero++;
        }

        return $slug;
    }
}
