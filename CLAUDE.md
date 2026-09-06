# CLAUDE.md — nachofernan (blog personal)

## Qué es este proyecto

Recreación en Laravel del blog personal del usuario, que hasta ahora vivía en WordPress
(theme "lovecraft", copia completa en `c:/xampp/htdocs/public_html`). El contrato de
hosting terminó y no se renovó; el nuevo VPS tiene 1GB de RAM y no puede sostener MySQL,
por eso el proyecto usa **SQLite** en todos los entornos.

El objetivo es doble:
1. Recrear el front del blog de forma **visualmente idéntica** al WordPress original.
2. Recrear en la administración únicamente el CRUD de entradas que el usuario realmente
   usaba (portada, formato entrada/cita, categoría, borrador/publicada) — nada más.

El plan de trabajo, fase por fase, vive en [`docs/roadmap.md`](docs/roadmap.md). Revisar
ese archivo antes de empezar cualquier tarea para saber en qué fase está el proyecto.

## Convenciones

- **Idioma del código**: clases, métodos, variables, rutas, nombres de vista y de
  columnas en español, autodescriptivos (ej. `Entrada`, `crearEntrada()`,
  `es_borrador`, `resources/views/entradas/mostrar.blade.php`).
- **Base de datos**: SQLite únicamente. Nunca agregar configuración ni dependencias de
  MySQL/PostgreSQL.
- **Sin sobreingeniería**: minimalista. Sólo se construye lo que el usuario usaba de
  verdad en el WordPress original (ver "Qué NO hacer" abajo). Nada de roles múltiples,
  comentarios, multi-idioma, ni funciones "por si acaso".
- **Sin reinventar la pólvora**: usar las convenciones estándar de Laravel (Eloquent,
  migraciones, Blade, rutas de recurso) antes de armar algo custom.
- **Paso a paso**: cada fase del roadmap se acuerda con el usuario antes de avanzar a la
  siguiente. Si una decisión no está tomada (ver "Preguntas abiertas" en el roadmap),
  preguntar antes de asumir.

## Flujo de trabajo: tres agentes

Para cualquier tarea no trivial, el trabajo se divide conceptualmente en tres roles.
Pueden ser tres subagentes reales (cuando el volumen lo justifica) o el mismo Claude
pasando por las tres etapas en orden — lo importante es no saltear ninguna.

### 1. Lector

- **Rol**: investigar antes de tocar nada.
- **Qué hace**: lee el código actual del proyecto Laravel, el WordPress original en
  `c:/xampp/htdocs/public_html` (theme, plantillas, `functions.php`), la base de datos
  MySQL existente (`nachofernan`, sólo como referencia) y `docs/roadmap.md`.
- **No** escribe código ni migra datos.
- **Entrega**: un resumen concreto de qué hay, qué falta, y qué decisión requiere al
  usuario (si aplica).

### 2. Editor

- **Rol**: implementar el paso puntual acordado del roadmap, nada más.
- **Qué hace**: escribe migraciones, modelos, controladores, vistas o CSS — acotado a
  ese paso.
- **No** inventa funcionalidad que no esté en el WordPress original ni en lo pedido.
- Sigue las convenciones de nombres en español y respeta la paleta/tipografía real del
  theme lovecraft (ver sección visual del roadmap).

### 3. Tester

- **Rol**: verificar que lo que hizo el Editor funciona y no rompe nada.
- **Qué hace**: levanta el sitio local (XAMPP / `php artisan serve`), compara
  visualmente contra el WordPress original, corre las migraciones limpias contra
  SQLite, corre los tests si existen.
- Si encuentra un problema, lo reporta — no lo parchea en silencio ni sigue de largo.
  Vuelve a Lector o Editor según corresponda.

## Fuente de verdad

- **Front visual**: WordPress en `c:/xampp/htdocs/public_html` (theme `lovecraft`),
  también levantable en `https://localhost/public_html/`.
- **Datos actuales**: MySQL local, base `nachofernan` (XAMPP) — sólo como referencia
  para migrar a SQLite, nunca como backend final.
- **Plan y decisiones**: [`docs/roadmap.md`](docs/roadmap.md).

**Ojo con el CSS personalizado**: el theme `lovecraft` se ve distinto a como sale
"de fábrica" porque el usuario cargó CSS propio en el Personalizador de WordPress
(Apariencia → Personalizar → CSS adicional). Eso NO está en `style.css` del theme:
vive en `wp_posts` como un post de tipo `custom_css`, y el que está activo es el
que apunta `wp_options.theme_mods_lovecraft` → `custom_css_post_id` (puede haber
más de un post `custom_css`; los viejos son revisiones sin usar). Antes de dar por
buena cualquier recreación visual, el rol Lector tiene que revisar ese CSS —
ya cambió una vez todo el layout (sacó el sidebar entero, el meta de fecha,
la navegación entre entradas y el pie), y podría volver a cambiar cosas.

## Qué NO hacer

- No usar MySQL en ningún entorno, ni siquiera "por ahora".
- No agregar plugins, campos o funciones de WordPress que el usuario no usaba
  realmente (verificado contra `wp_postmeta` y taxonomías en uso).
- No crear post types, taxonomías o campos que no estén en uso real: sólo entradas
  (`post`), categorías, formato entrada/cita (`post-format-aside`) e imagen destacada.
- No adelantarse de fase del roadmap sin acuerdo del usuario.
