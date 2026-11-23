<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Se ejecuta en cada arranque del contenedor, pero solo siembra datos
     * cuando la base está vacía (no pisa información capturada).
     */
    public function run(): void
    {
        $this->call(RolesPermisosSeeder::class);

        if (User::exists()) {
            $this->command?->info('La base ya tiene datos: se omite la carga de demostración.');

            return;
        }

        $this->call(DemoSeeder::class);
    }
}
