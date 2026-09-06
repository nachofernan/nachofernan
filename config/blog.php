<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Preferencias personales del blog
    |--------------------------------------------------------------------------
    |
    | Datos propios del sitio (no del framework), migrados de las opciones de
    | WordPress. Van en el .env porque es un blog de un único autor: no hace
    | falta una tabla de configuración para esto.
    |
    */

    'bajada' => env('BLOG_BAJADA', 'Algunas de las cosas que se me ocurren'),

];
