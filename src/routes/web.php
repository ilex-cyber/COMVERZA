<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Panel;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sitio público
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/servicios', [SiteController::class, 'servicios'])->name('servicios');
Route::get('/plataforma', [SiteController::class, 'plataforma'])->name('plataforma');
Route::get('/catalogo', [SiteController::class, 'catalogo'])->name('catalogo');
Route::get('/contacto', [SiteController::class, 'contacto'])->name('contacto');
Route::post('/contacto', [SiteController::class, 'enviarContacto'])
    ->middleware('throttle:5,1')->name('contacto.enviar');

/*
|--------------------------------------------------------------------------
| Autenticación y recuperación de contraseña (autoservicio)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.intento');

    Route::get('/olvide-mi-contrasena', [PasswordController::class, 'solicitar'])->name('password.request');
    Route::post('/olvide-mi-contrasena', [PasswordController::class, 'enviarEnlace'])
        ->middleware('throttle:3,1')->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [PasswordController::class, 'formulario'])->name('password.reset');
    Route::post('/restablecer-contrasena', [PasswordController::class, 'restablecer'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Panel administrativo (RBAC)
|--------------------------------------------------------------------------
*/
Route::prefix('panel')->name('panel.')->middleware(['auth', 'activo'])->group(function () {

    Route::get('/', [Panel\DashboardController::class, 'index'])
        ->middleware('permission:dashboard.ver')->name('dashboard');
    Route::get('/graficas', [Panel\DashboardController::class, 'graficas'])
        ->middleware('permission:dashboard.ver')->name('graficas');
    Route::get('/consola-api', [Panel\DashboardController::class, 'consolaApi'])
        ->middleware('permission:dashboard.ver')->name('consola-api');

    Route::resource('categorias', Panel\CategoriaController::class)->parameters(['categorias' => 'categoria'])->except('show');
    Route::resource('proveedores', Panel\ProveedorController::class)->parameters(['proveedores' => 'proveedor']);
    Route::resource('clientes', Panel\ClienteController::class);
    Route::resource('productos', Panel\ProductoController::class);
    Route::delete('productos/{producto}/imagenes/{imagen}', [Panel\ProductoController::class, 'eliminarImagen'])
        ->middleware('permission:productos.editar')->name('productos.imagenes.destroy');
    Route::get('productos/{producto}/qr', [Panel\ProductoController::class, 'qr'])
        ->middleware('permission:productos.ver')->name('productos.qr');
    Route::resource('pedidos', Panel\PedidoController::class);
    Route::get('pedidos/{pedido}/pdf', [Panel\PedidoController::class, 'pdf'])
        ->middleware('permission:pedidos.ver')->name('pedidos.pdf');
    Route::post('pedidos/{pedido}/enviar', [Panel\PedidoController::class, 'enviar'])
        ->middleware('permission:pedidos.ver')->name('pedidos.enviar');
    Route::resource('banners', Panel\BannerController::class)->except('show');

    Route::get('mensajes', [Panel\MensajeController::class, 'index'])->middleware('permission:mensajes.ver')->name('mensajes.index');
    Route::get('mensajes/{mensaje}', [Panel\MensajeController::class, 'show'])->middleware('permission:mensajes.ver')->name('mensajes.show');
    Route::delete('mensajes/{mensaje}', [Panel\MensajeController::class, 'destroy'])->middleware('permission:mensajes.eliminar')->name('mensajes.destroy');

    // Reportes: PDF (DomPDF), Excel (PhpSpreadsheet) y envío por correo
    Route::middleware('permission:reportes.exportar')->group(function () {
        Route::get('reportes/productos.pdf', [Panel\ReporteController::class, 'productosPdf'])->name('reportes.productos.pdf');
        Route::get('reportes/ventas.pdf', [Panel\ReporteController::class, 'ventasPdf'])->name('reportes.ventas.pdf');
        Route::get('reportes/{tipo}.xlsx', [Panel\ReporteController::class, 'excel'])
            ->whereIn('tipo', ['productos', 'clientes', 'proveedores', 'pedidos'])->name('reportes.excel');
        Route::post('reportes/enviar', [Panel\ReporteController::class, 'enviar'])->name('reportes.enviar');
    });

    // Seguridad
    Route::resource('usuarios', Panel\UsuarioController::class)->except('show');
    Route::resource('roles', Panel\RolController::class)->parameters(['roles' => 'rol'])->except('show');
    Route::resource('permisos', Panel\PermisoController::class)->parameters(['permisos' => 'permiso'])->except('show');
    Route::get('bitacora', [Panel\BitacoraController::class, 'index'])->middleware('permission:bitacora.ver')->name('bitacora');

    Route::get('perfil', [Panel\PerfilController::class, 'edit'])->name('perfil');
    Route::put('perfil', [Panel\PerfilController::class, 'update'])->name('perfil.update');
    Route::put('perfil/password', [Panel\PerfilController::class, 'password'])->name('perfil.password');
});
