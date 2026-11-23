<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected $seeder = DatabaseSeeder::class;

    public function test_paginas_publicas_responden(): void
    {
        foreach (['/', '/servicios', '/plataforma', '/catalogo', '/contacto', '/login', '/olvide-mi-contrasena'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
