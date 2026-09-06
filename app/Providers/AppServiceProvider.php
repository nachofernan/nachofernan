<?php

namespace App\Providers;

use App\Models\Categoria;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Orden real del menú de categorías del blog original (WordPress), con la
     * etiqueta que usaba para "sin categoría".
     *
     * @var array<string, string>
     */
    private const MENU_CATEGORIAS = [
        'amns' => 'AMNS',
        'cuentos' => 'Cuentos',
        'imagenes' => 'Imágenes',
        'inconscientes' => 'Inconscientes',
        'sin-categoria' => 'Mínimos',
        'cartas' => 'Cartas',
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('partials.menu', function ($view) {
            $categoriasPorSlug = Categoria::all()->keyBy('slug');

            $categorias = collect(self::MENU_CATEGORIAS)
                ->map(fn ($etiqueta, $slug) => $categoriasPorSlug->has($slug)
                    ? (object) ['slug' => $slug, 'nombre' => $etiqueta]
                    : null)
                ->filter()
                ->values();

            $view->with('categoriasMenu', $categorias);
        });
    }
}
