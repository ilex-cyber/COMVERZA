<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SiteController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/servicios', [SiteController::class, 'servicios'])
    ->name('servicios');

Route::get('/plataforma', [SiteController::class, 'plataforma'])
    ->name('plataforma');

Route::get('/contacto', [SiteController::class, 'contacto'])
    ->name('contacto');

Route::post('/contacto', [SiteController::class, 'enviarContacto'])
    ->name('contacto.enviar');