<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $usuario = config('admin.usuario');

        if (blank($usuario['password'])) {
            $this->command?->warn('ADMIN_PASSWORD no está definido en .env — no se creó el usuario admin.');

            return;
        }

        User::updateOrCreate(
            ['email' => $usuario['email']],
            ['name' => $usuario['nombre'], 'password' => $usuario['password']],
        );
    }
}
