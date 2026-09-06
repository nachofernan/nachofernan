<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Entrada;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDO;

class MigrarWordpress extends Command
{
    protected $signature = 'wordpress:migrar
        {--host=127.0.0.1 : Host de la base MySQL de origen}
        {--base-datos=nachofernan : Nombre de la base MySQL de origen}
        {--usuario=root : Usuario de la base MySQL de origen}
        {--clave= : Contraseña de la base MySQL de origen}
        {--uploads=C:/xampp/htdocs/public_html/wp-content/uploads : Ruta local a wp-content/uploads del WordPress original}';

    protected $description = 'Migra categorías, entradas y portadas desde la base MySQL del WordPress original a SQLite.';

    public function handle(): int
    {
        $pdo = new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8', $this->option('host'), $this->option('base-datos')),
            $this->option('usuario'),
            (string) $this->option('clave'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $categoriasPorTermino = $this->migrarCategorias($pdo);
        $this->migrarEntradas($pdo, $categoriasPorTermino);

        $this->info('Migración completa. Corré "php artisan storage:link" si todavía no lo hiciste, para que las portadas se sirvan públicamente.');

        return self::SUCCESS;
    }

    /**
     * @return array<int, int> mapa term_id de WordPress => id de Categoria en la app
     */
    private function migrarCategorias(PDO $pdo): array
    {
        $filas = $pdo->query("
            SELECT t.term_id, t.name, t.slug
            FROM wp_terms t
            JOIN wp_term_taxonomy tt ON tt.term_id = t.term_id
            WHERE tt.taxonomy = 'category'
        ")->fetchAll();

        $mapa = [];

        foreach ($filas as $fila) {
            $categoria = Categoria::updateOrCreate(
                ['slug' => $fila['slug']],
                ['nombre' => html_entity_decode($fila['name'])]
            );

            $mapa[(int) $fila['term_id']] = $categoria->id;
        }

        $this->info(sprintf('Categorías migradas: %d', count($mapa)));

        return $mapa;
    }

    /**
     * @param  array<int, int>  $categoriasPorTermino
     */
    private function migrarEntradas(PDO $pdo, array $categoriasPorTermino): void
    {
        $posts = $pdo->query("
            SELECT ID, post_title, post_name, post_content, post_date, post_status
            FROM wp_posts
            WHERE post_type = 'post' AND post_status IN ('publish', 'draft')
            ORDER BY post_date ASC
        ")->fetchAll();

        $consultaCategoria = $pdo->prepare("
            SELECT tt.term_id
            FROM wp_term_relationships tr
            JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            WHERE tr.object_id = ? AND tt.taxonomy = 'category'
            LIMIT 1
        ");

        $consultaFormato = $pdo->prepare("
            SELECT t.slug
            FROM wp_term_relationships tr
            JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN wp_terms t ON t.term_id = tt.term_id
            WHERE tr.object_id = ? AND tt.taxonomy = 'post_format'
            LIMIT 1
        ");

        $consultaPortada = $pdo->prepare("
            SELECT archivo.meta_value AS ruta
            FROM wp_postmeta miniatura
            JOIN wp_postmeta archivo
                ON archivo.post_id = miniatura.meta_value
                AND archivo.meta_key = '_wp_attached_file'
            WHERE miniatura.post_id = ? AND miniatura.meta_key = '_thumbnail_id'
            LIMIT 1
        ");

        $migradas = 0;
        $portadasCopiadas = 0;
        $portadasPorDefecto = 0;
        $portadaPorDefecto = null;
        $slugsUsados = [];

        foreach ($posts as $post) {
            $consultaCategoria->execute([$post['ID']]);
            $termIdCategoria = $consultaCategoria->fetchColumn();

            $consultaFormato->execute([$post['ID']]);
            $slugFormato = $consultaFormato->fetchColumn();

            $resultadoPortada = $this->migrarPortada($consultaPortada, $post['ID']);
            $portada = $resultadoPortada['portada'];

            if ($portada) {
                $portadasCopiadas++;
                // Los posts vienen ordenados por fecha; la primera portada que
                // encontramos en disco es la del primer post con foto real.
                $portadaPorDefecto ??= $portada;
            } elseif ($resultadoPortada['rota'] && $portadaPorDefecto) {
                // WordPress tenía una portada asignada pero el archivo no está en
                // disco (foto perdida al migrar los archivos): usamos como
                // reemplazo la portada del primer post, tal como pidió el usuario.
                $portada = $portadaPorDefecto;
                $portadasPorDefecto++;
            }

            $slug = $this->resolverSlug($post, $slugsUsados);

            Entrada::updateOrCreate(
                ['slug' => $slug],
                [
                    'categoria_id' => $categoriasPorTermino[(int) $termIdCategoria] ?? null,
                    'titulo' => html_entity_decode($post['post_title']),
                    'contenido' => $this->limpiarContenido($post['post_content']),
                    'portada' => $portada,
                    'tipo' => $slugFormato === 'post-format-aside' ? 'cita' : 'entrada',
                    'estado' => $post['post_status'] === 'publish' ? 'publicada' : 'borrador',
                    'publicada_en' => $post['post_date'],
                ]
            );

            $migradas++;
        }

        $this->info(sprintf('Entradas migradas: %d', $migradas));
        $this->info(sprintf('Portadas copiadas: %d', $portadasCopiadas));
        $this->info(sprintf('Portadas reemplazadas por la del primer post (archivo no encontrado): %d', $portadasPorDefecto));
    }

    /**
     * WordPress deja post_name vacío en algunos borradores que nunca se guardaron
     * con título definitivo. Generamos un slug a partir del título y, si igual
     * colisiona, lo desambiguamos con el ID original del post.
     *
     * @param  array<string, mixed>  $post
     * @param  array<string, bool>  $slugsUsados
     */
    private function resolverSlug(array $post, array &$slugsUsados): string
    {
        $slug = $post['post_name'] !== '' ? $post['post_name'] : Str::slug($post['post_title']);

        if ($slug === '') {
            $slug = 'entrada-'.$post['ID'];
        }

        if (isset($slugsUsados[$slug])) {
            $slug .= '-'.$post['ID'];
        }

        $slugsUsados[$slug] = true;

        return $slug;
    }

    private function limpiarContenido(string $contenido): string
    {
        // WordPress guarda el contenido con comentarios de bloques de Gutenberg
        // (<!-- wp:paragraph -->) alrededor del HTML real; sólo nos interesa el HTML.
        $contenido = preg_replace('#<!--\s*/?wp:.*?-->#s', '', $contenido);

        return $this->autop(trim($contenido));
    }

    /**
     * Las entradas viejas (anteriores al editor de bloques) no traen el
     * contenido envuelto en <p>: WordPress se lo aplicaba al vuelo en el
     * front con wpautop(). Como acá no corre WordPress, replicamos lo
     * esencial de esa función una sola vez, al migrar.
     */
    private function autop(string $texto): string
    {
        $etiquetasDeBloque = 'p|div|figure|blockquote|ul|ol|li|table|thead|tbody|tr|td|th|h[1-6]|pre|form|fieldset|iframe|hr|section|article|aside|header|footer|nav';

        $texto = str_replace(["\r\n", "\r"], "\n", $texto);
        $texto = preg_replace('/\n\s*\n+/', "\n\n", $texto);

        $bloques = preg_split('/\n\s*\n/', trim($texto));

        $html = [];

        foreach ($bloques as $bloque) {
            $bloque = trim($bloque);

            if ($bloque === '') {
                continue;
            }

            if (preg_match('/^<('.$etiquetasDeBloque.')[\s>]/i', $bloque)) {
                $html[] = $bloque;
            } else {
                $html[] = '<p>'.nl2br($bloque, false).'</p>';
            }
        }

        return implode("\n\n", $html);
    }

    /**
     * @return array{portada: ?string, rota: bool} "rota" indica que WordPress
     *         tenía una portada asignada pero el archivo no se encontró en disco.
     */
    private function migrarPortada(\PDOStatement $consultaPortada, int $postId): array
    {
        $consultaPortada->execute([$postId]);
        $rutaRelativa = $consultaPortada->fetchColumn();

        if (! $rutaRelativa) {
            return ['portada' => null, 'rota' => false];
        }

        $origen = rtrim($this->option('uploads'), '/').'/'.$rutaRelativa;

        if (! is_file($origen)) {
            $this->warn("Portada no encontrada en disco: {$origen}");

            return ['portada' => null, 'rota' => true];
        }

        $destino = 'portadas/'.basename($rutaRelativa);

        if (! Storage::disk('public')->exists($destino)) {
            Storage::disk('public')->put($destino, file_get_contents($origen));
        }

        return ['portada' => $destino, 'rota' => false];
    }
}
