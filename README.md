# nachofernan — blog personal

Recreación en Laravel del blog personal, que hasta ahora vivía en WordPress (theme
"lovecraft"). El objetivo es reproducir el front de forma visualmente idéntica y
recrear en la administración únicamente el CRUD de entradas que se usaba en la
práctica (portada, formato entrada/cita, categoría, borrador/publicada).

Se mueve a Laravel + SQLite porque el nuevo VPS tiene 1GB de RAM y no puede sostener
MySQL.

## Stack

- Laravel 12 / PHP 8.5
- SQLite (mismo motor en local y en producción)
- Blade + CSS propio (sin frameworks de UI)

## Documentación del proyecto

- [`CLAUDE.md`](CLAUDE.md) — convenciones del proyecto y metodología de trabajo
  (lector / editor / tester).
- [`docs/roadmap.md`](docs/roadmap.md) — plan de trabajo fase por fase, con lo ya
  relevado del WordPress original y las decisiones tomadas.

## Poner el proyecto en marcha

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

## Licencia

Proyecto personal. Basado en el framework [Laravel](https://laravel.com), licenciado
bajo [MIT](https://opensource.org/licenses/MIT).
