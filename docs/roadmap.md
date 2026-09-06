# Roadmap — recreación del blog en Laravel

Plan de trabajo fase por fase. Cada fase se acuerda con el usuario antes de arrancar
la siguiente. No sobreingeniería: sólo lo que se usaba de verdad en el WordPress
original.

## Decisiones ya tomadas

- **Base de datos**: SQLite en local y en producción (el VPS tiene 1GB de RAM, no
  soporta MySQL).
- **Idioma del código**: nombres de clases, métodos, rutas y vistas en español.
- **Alcance de administración**: CRUD de entradas nada más (crear, editar, borrar,
  portada, tipo entrada/cita, categoría, borrador/publicada). Sin roles, sin
  comentarios, sin multi-usuario, salvo que se pida explícitamente.
- **Orden de trabajo**: primero migración de datos (base de datos + imágenes), recién
  después el front. Así el front se construye directamente contra datos reales, sin
  maquetar dos veces.

## Relevamiento del WordPress original (hecho)

Fuente: `c:/xampp/htdocs/public_html`, theme `lovecraft`, base MySQL local
`nachofernan` (XAMPP).

- **Contenido real en uso**: sólo el post type `post`. Nada de `page` ni custom post
  types.
  - 84 entradas publicadas, 8 borradores.
  - 80 archivos adjuntos (imágenes de portada / contenido).
- **Formato de entrada**: taxonomía `post_format`, con un único formato en uso,
  `post-format-aside` (15 entradas) — esto es la "cita/nota corta" que mencionó el
  usuario. El resto son entradas comunes.
- **Categorías en uso** (taxonomía `category`, 6 términos):
  `sin-categoria`, `inconscientes`, `cuentos`, `imagenes`, `cartas`, `amns`.
- **Imagen destacada**: meta `_thumbnail_id`, presente en 69 de 92 entradas —
  corresponde al campo "portada" que mencionó el usuario.
- **Metadatos revisados y descartados** (no se usan / son ruido de WP, no se migran):
  `_edit_lock`, `_edit_last`, `_wp_old_slug`, metadatos de menú de navegación,
  metadatos de header/adjuntos internos de WP.
- **Paleta y tipografía del theme `lovecraft`** (de `style.css`):
  - Fondo general: `#fafafa`.
  - Texto principal: `#111`.
  - Acento (links/detalles): `#CA2017` (rojo).
  - Fondo alternativo/footer: `#1d1d1d` / `#111` con texto blanco.
  - Tipografía de títulos: `Playfair Display` (serif, con fallback Georgia).
  - Tipografía de texto/UI: `Lato` (sans-serif, con fallback Helvetica).

## Fase 0 — Fundación (hecho)

- Proyecto Laravel creado (`nachofernan`).
- `CLAUDE.md` con convenciones y metodología (lector / editor / tester).
- Este roadmap.

## Fase 1 — Migración de datos (BD + imágenes) — hecho

Objetivo: modelos y migraciones en SQLite, con los datos reales migrados —
contenido de entradas e imágenes incluidas. Esto va primero para construir el front
directamente contra datos reales.

- [x] Definir modelo `Entrada` (`app/Models/Entrada.php`): título, slug, contenido,
      portada, tipo [`entrada` | `cita`], estado [`borrador` | `publicada`], fecha de
      publicación, categoría. (Se descartó el campo "extracto": no se usaba en
      ninguna entrada del WordPress original.)
- [x] Definir modelo `Categoria` (`app/Models/Categoria.php`): nombre, slug.
- [x] Migraciones SQLite correspondientes (`database/migrations/..._crear_tabla_*`).
- [x] Comando de importación `php artisan wordpress:migrar` (en
      `app/Console/Commands/MigrarWordpress.php`): lee por PDO directo la base MySQL
      `nachofernan` (sólo lectura, no es una dependencia del proyecto) y migra
      categorías, entradas, formato de entrada y portada. Es idempotente
      (`updateOrCreate` por slug), se puede volver a correr sin duplicar datos.
  - El contenido de WordPress viene en bloques de Gutenberg
    (`<!-- wp:paragraph -->`); el comando los limpia y deja sólo el HTML.
  - Ningún post tenía imágenes embebidas en el contenido (sólo portada vía
    `_thumbnail_id`), así que no hizo falta reescribir URLs dentro del texto.
  - 7 borradores no tenían `post_name` en WordPress (nunca se guardaron con slug
    definitivo); el comando genera uno a partir del título y, si igual colisiona, lo
    desambigua con el ID original.
- [x] Copiar los archivos de portada a `storage/app/public/portadas`, servidos
      públicamente vía `php artisan storage:link`.

**Resultado verificado (primera corrida)**: 92 entradas migradas (84 publicadas + 8
borradores), 15 de tipo "cita", 6 categorías, 69 portadas copiadas y accesibles —
coincide exactamente con lo relevado del WordPress original.

**Corrección posterior**: la base MySQL local `nachofernan` (XAMPP) era una copia
vieja del sitio, a la que le faltaban 8 entradas publicadas más recientes
(2024–2026). El usuario acercó un dump más completo y actualizado,
`nachofernan_com_ar.sql`, en `c:/xampp/htdocs/`. Se reemplazó por completo la base
`nachofernan` local con ese dump (`DROP DATABASE` + reimportar) y se volvió a correr
`wordpress:migrar`. Resultado final: **100 entradas** (92 publicadas + 8
borradores), 16 de tipo "cita", 6 categorías (sin cambios), 76 portadas copiadas —
las 76 portadas referenciadas en WordPress se encontraron todas en disco, ninguna
quedó sin copiar.

Como pedido de resguardo a futuro (por si en el VPS faltara algún archivo de
imagen), el comando ahora asigna automáticamente la portada de la primera entrada
publicada como imagen por defecto cuando WordPress tenía una portada asignada pero
el archivo no está en disco — no fue necesario en esta corrida (0 casos), pero
queda cubierto el escenario.

## Fase 2 — Front: rutas y vistas con datos reales — hecho

Objetivo: recrear visualmente el WordPress original con vistas Blade conectadas de
entrada a los datos migrados en la fase 1 (sin maquetar en falso primero y cablear
después).

Relevamiento de las plantillas del theme (hecho):

- **Estructura de página** (`header.php` + `index.php`/`singular.php` +
  `sidebar.php` + `footer.php`): título/tagline del sitio centrados arriba, menú de
  navegación debajo, imagen de cabecera grande (la del post si es una entrada
  individual con portada; si no, la imagen de cabecera del sitio), contenido a la
  izquierda, sidebar a la derecha, créditos al pie.
- **Menú real en uso** (`wp_posts` tipo `nav_menu_item`): son las 6 categorías como
  links, en este orden — AMNS, Cuentos, Imágenes, Inconscientes, "Mínimos" (nombre
  visible para la categoría `sin-categoria`), Cartas.
- **Sidebar real en uso** (`sidebars_widgets` sin widgets asignados → cae al
  fallback del theme): Buscador, Entradas recientes (5), Categorías, Archivo por
  fecha.
- **Título y bajada del sitio** (`wp_options`): `NachoFernan` /
  `Algunas de las cosas que se me ocurren`.
- **Imagen de cabecera del sitio** (fuera de una entrada individual): archivo local
  `wp-content/uploads/2021/06/cropped-wallpaperbetter.com_7680x4320_2-scaled-1.jpg`.
- **Tarjeta de entrada** (`content.php`): si es "cita" (`post-format-aside`) no
  muestra título, sólo el contenido corto y los metadatos (fecha, categoría) debajo;
  si es entrada común, muestra portada (si tiene), título enlazado, metadatos y
  contenido/extracto.
- **Entrada individual** (`singular.php`): título, metadatos, contenido completo,
  navegación a entrada anterior/siguiente.

Tareas:

- [x] Paleta/tipografía portadas a `public/css/blog.css`, con las fuentes reales del
      theme auto-hospedadas en `public/fonts` (mismos `.woff2`, sin depender de
      Google Fonts ni de WordPress).
- [x] Layout base (`resources/views/layouts/blog.blade.php`): cabecera, menú de
      categorías, imagen de portada de sitio/entrada, contenido a ancho completo
      (ver corrección de CSS personalizado más abajo — no lleva sidebar ni pie).
- [x] Ruta y vista de listado de entradas (home, paginado), sólo publicadas
      (`EntradaController@index`).
- [x] Ruta y vista de entrada individual por slug (`EntradaController@mostrar`).
- [x] Ruta de filtro por categoría (`EntradaController@porCategoria`, reutiliza la
      vista de listado).
- [x] Tarjeta de entrada (`entradas/_tarjeta.blade.php`) con variante visual para el
      formato "cita": sin título.

Hallazgos durante la implementación:

- El contenido de las entradas migradas en la Fase 1 venía sin `wpautop()`
  aplicado: las entradas anteriores al editor de bloques tenían párrafos separados
  por simples saltos de línea, sin `<p>`. Se agregó una versión reducida de
  `wpautop` al comando `wordpress:migrar` (método `autop()`) para que el HTML
  migrado quede ya bien formado.
  - Queda un caso aislado sin resolver: la entrada `el-flujo` (2013) tiene un
    fragmento pegado desde Evernote con una etiqueta `<p>` manual seguida de texto
    suelto en la misma línea; el primer párrafo después de esa etiqueta no separa
    bien. Es la única entrada con este patrón — se puede corregir a mano si
    molesta visualmente, no amerita una solución genérica para un sólo caso.
- Se le agregó estilo a los bloques `wp-block-pullquote` de Gutenberg (16
  entradas, todas del tipo "cita"), portado del `style.css` original, porque sin
  eso las citas se veían sin la comilla decorativa ni el tamaño de fuente mayor.
- `APP_LOCALE` se cambió a `es` para que fechas y meses salgan en español.

### Corrección: CSS personalizado real del sitio (el "toque" del usuario)

El usuario levantó el WordPress original en `https://localhost/public_html/` y
avisó que el sidebar ("menú derecho") no existe en su versión real. Repasando
`wp_options`, WordPress guarda el "CSS adicional" del Personalizador como un post
de tipo `custom_css` — `theme_mods_lovecraft.custom_css_post_id` apuntaba al post
**743** (no al 29, una revisión vieja de 2021). Ese CSS es el toque personal real
del usuario:

```css
.post-meta { display: none; }                 /* sin fecha/categoría visible */
.post-title { padding-bottom: 20px; border-bottom: 1px solid #EEE; }
.post-image, .post-image img { width: 100%; } /* portada a ancho completo */
.respond-container, .archive-header, .sidebar { display: none; } /* sin comentarios, sin banner de categoría/búsqueda, SIN SIDEBAR */
.content { width: 100%; }
.post-inner { width: 100%; max-width: 70%; }  /* contenido centrado al 70% */
.credits-inner { display: none; }              /* sin pie "Powered by WordPress" */
.post-navigation { display: none; }            /* sin anterior/siguiente */
```

Esto cambió bastante lo ya construido en esta fase. Se sacó del proyecto (decisión
confirmada con el usuario, ya que estas funciones no se ven nunca en el sitio
real):

- El sidebar completo (`partials/sidebar.blade.php`, eliminado) y su
  `View::composer` en `AppServiceProvider`.
- El buscador (`EntradaController@buscar`, ruta `/buscar`) — vivía sólo en el
  sidebar oculto.
- El archivo por mes (`?mes=YYYY-MM` en la home) — mismo motivo.
- El meta de fecha/categoría bajo el título (`entradas/_meta.blade.php`,
  eliminado).
- La navegación anterior/siguiente en la entrada individual.
- El banner negro de título en listado por categoría (`.archive-header`).
- El pie de créditos.

Y se ajustó el layout: `.contenido` a ancho completo, `.entrada-cuerpo` centrado a
`max-width: 70%`, título con borde inferior, portada de listado a ancho completo.

**Preferencias del sitio en `.env`**: siguiendo el pedido del usuario de no armar
una tabla de configuración para esto (es un blog de un solo autor), la bajada del
sitio ("Algunas de las cosas que se me ocurren") vive en `BLOG_BAJADA` (`.env`,
leído desde `config/blog.php`) en vez de estar hardcodeada en la vista. El nombre
del sitio ya usaba `APP_NAME`.

**Corrección de datos (Fase 1)**: en el camino también apareció que la base MySQL
local `nachofernan` estaba desactualizada — el usuario acercó
`c:/xampp/htdocs/nachofernan_com_ar.sql`, un dump más completo, y se reemplazó la
base con ese dump. Detalle completo en la sección de la Fase 1 más arriba.

**Cómo probarlo en local**: `php artisan serve` y abrir la URL que indica en el
navegador. (XAMPP/Apache serviría el proyecto desde la raíz en vez de `public/`,
así que hace falta un vhost apuntando a `public/` si se prefiere Apache en vez de
`php artisan serve`.)

### Corrección: tipografía base y recorte de imagen en el listado

El usuario detectó, comparando contra `https://localhost/public_html/`, dos
errores de esta fase:

- **Tipografía base equivocada**: el CSS del theme define `body { font-family:
  'Playfair Display', 'Georgia', serif; font-size: 17px; }` — es decir, el texto
  normal (bajada del sitio, menú de categorías, contenido de las entradas) es
  **serif**. `Lato` sólo se usaba para elementos chicos de interfaz (meta,
  widgets, título de archivo) que ya se sacaron en la corrección anterior. Se
  había puesto `Lato` como fuente base por error; se corrigió a
  `'Playfair Display', Georgia, serif` a 17px.
- **A la imagen de portada del listado le faltaba `figure { margin: 0; }`**: el
  theme original resetea el margen por default del navegador en `<figure>`
  (`margin: 0`); al no portar ese reset, la imagen quedaba con el margen por
  default del navegador (~40px a los costados), como si tuviera un marco blanco.
  El sitio real, en cambio, la muestra a ancho completo dentro de la card (así lo
  fuerza también el CSS personalizado: `.post-image, .post-image img { width:
  100%; }`). Se agregó el reset global de `figure` y se ajustaron los enlaces
  (`a { color: #ca2017; text-decoration: underline; }` como base, con
  `color: inherit` explícito en título del sitio, menú y título de entrada) para
  que no queden todos en rojo por herencia.

## Fase 3 — Administración (CRUD minimalista) — hecho

- [x] Autenticación simple (un solo usuario, el autor del blog).
- [x] CRUD de entradas: crear, editar, borrar.
- [x] Subida de portada.
- [x] Selector de tipo (entrada / cita) y categoría.
- [x] Estado borrador/publicada.

Decisiones tomadas con el usuario antes de implementar:

- **Auth manual mínima**: sin paquetes nuevos (ni Breeze ni Jetstream). Se usa la
  tabla `users` que ya trae Laravel, con un solo registro creado por el seeder desde
  `config/admin.php` (que lee `ADMIN_NOMBRE` / `ADMIN_EMAIL` / `ADMIN_PASSWORD` del
  `.env`, mismo patrón que `BLOG_BAJADA`). `App\Http\Controllers\Admin\SesionController`
  maneja login/logout con `Auth::attempt()`; sin registro, sin recuperación de
  contraseña, sin roles.
  - **Antes de sembrar en producción**: cambiar `ADMIN_PASSWORD` en `.env` (el valor
    de ejemplo es `cambiar-esta-clave`) y correr `php artisan db:seed` — es
    idempotente (`updateOrCreate` por email).
- **Editor de contenido — Trix**: en vez de un `<textarea>` con HTML crudo, se
  autohospedó el editor [Trix](https://trix-editor.org) (`public/vendor/trix/`,
  descargado una única vez, sin CDN ni paso de build) para escribir el contenido de
  forma visual, consistente con cómo se autohospedaron las fuentes en la Fase 2.

Implementación:

- `App\Http\Controllers\Admin\EntradaController` (`resources/views/admin/entradas/`):
  `index` (listado, todos los estados), `create`/`store`, `edit`/`update`, `destroy`.
  Reusa `Entrada::conCategoria()` / `recientesPrimero()` de la Fase 2.
  - El slug se genera una sola vez al crear (a partir del título) y no se toca al
    editar, para no romper permalinks ya publicados — no es un campo del formulario.
  - La portada vieja se borra del disco (`Storage::disk('public')`) al reemplazarla o
    al borrar la entrada, para no dejar archivos huérfanos.
- Rutas: `/admin/iniciar-sesion` (GET/POST, middleware `guest`), `/admin/entradas/*`
  (resource, middleware `auth`, `->except('show')` porque la vista pública ya cubre
  eso). Definidas **antes** del catch-all `/{slug}` del blog público para no chocar.
  Invitados sin sesión son redirigidos a `/admin/iniciar-sesion`
  (`$middleware->redirectGuestsTo(...)` en `bootstrap/app.php`).
- **Importante — `Entrada::getRouteKeyName()` es `slug`**: el route model binding de
  `/admin/entradas/{entrada}` liga por slug, no por ID (mismo comportamiento que en
  el front público). Los links del admin usan `route('admin.entradas.edit', $entrada)`
  con el modelo, nunca el ID a mano.
- `public/css/admin.css`: mismos tokens de color/tipografía que `blog.css` (fondo
  `#fafafa`, acento `#CA2017`, cabecera oscura `#1d1d1d`), sin depender de Tailwind ni
  del pipeline de Vite (que quedó sin usar desde la Fase 2).

**Verificado en local** (`php artisan serve` + `curl`, con sesión autenticada real):
login, creación de entrada con portada, la entrada aparece en el blog público sólo
si `estado=publicada` (404 en borrador), edición cambia tipo/estado/categoría/
contenido, borrado elimina la entrada y su archivo de portada sin dejar huérfanos
(conteo de portadas volvió a 76), logout y redirección de invitados funcionando.

## Fase 4 — Pulido y despliegue

- [ ] Verificar consumo de recursos en el VPS (1GB RAM, sin MySQL).
- [ ] Backups de la base SQLite.
- [ ] Checklist final de paridad visual contra el WordPress original.

## Preguntas abiertas

Se completan a medida que aparecen, fase por fase — no se responden todas de
antemano para no sobreanalizar antes de tener el front visual andando.
