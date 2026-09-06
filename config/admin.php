<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Usuario del panel de administración
    |--------------------------------------------------------------------------
    |
    | Blog de un único autor: no hace falta una pantalla de registro ni una
    | tabla de roles. El seeder usa estos datos para crear (o actualizar) el
    | único usuario que puede entrar a /admin.
    |
    */

    'usuario' => [
        'nombre' => env('ADMIN_NOMBRE', 'Nacho Fernández'),
        'email' => env('ADMIN_EMAIL', 'nachofernan@gmail.com'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
