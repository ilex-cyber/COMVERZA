<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Esquema de negocio de COMVERZA.
 *
 * Reglas de integridad:
 *  - Las llaves foráneas de catálogos usan RESTRICT: no se puede borrar una
 *    categoría/proveedor/cliente/producto que esté en uso (la aplicación lo
 *    valida con un mensaje claro y la base de datos lo garantiza).
 *  - Los detalles de pedido y la galería de imágenes usan CASCADE.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->string('slug', 100)->unique();
            $table->string('descripcion', 500)->nullable();
            $table->string('color', 7)->default('#1677ff');
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 150);
            $table->string('rfc', 13)->unique();
            $table->string('contacto', 100);
            $table->string('email', 150)->unique();
            $table->string('telefono', 20);
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 80)->nullable();
            $table->string('sitio_web', 150)->nullable();
            $table->string('logo')->nullable();
            $table->string('constancia_fiscal')->nullable();   // PDF
            $table->unsignedTinyInteger('calificacion')->default(5);
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['Empresa', 'Persona'])->default('Empresa');
            $table->string('nombre', 120);
            $table->string('empresa', 150)->nullable();
            $table->string('rfc', 13)->nullable()->unique();
            $table->string('email', 150)->unique();
            $table->string('telefono', 20);
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 80)->nullable()->index();
            $table->string('codigo_postal', 5)->nullable();
            $table->decimal('limite_credito', 12, 2)->default(0);
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 30)->unique();
            $table->string('nombre', 150);
            $table->string('slug', 170)->unique();
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->restrictOnDelete();
            $table->decimal('costo', 12, 2)->default(0);
            $table->decimal('precio', 12, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('stock_minimo')->default(5);
            $table->string('unidad', 20)->default('pieza');
            $table->string('imagen')->nullable();
            $table->string('ficha_tecnica')->nullable();   // PDF
            $table->boolean('destacado')->default(false)->index();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('producto_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('ruta');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha')->index();
            $table->date('fecha_entrega')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'enviado', 'entregado', 'cancelado'])
                ->default('pendiente')->index();
            $table->enum('metodo_pago', ['transferencia', 'tarjeta', 'efectivo', 'credito'])->default('transferencia');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('notas', 1000)->nullable();
            $table->timestamps();
        });

        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('importe', 12, 2);
            $table->timestamps();
        });

        // Fotos de presentación (carrusel) de la landing
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 120);
            $table->string('subtitulo', 255)->nullable();
            $table->string('imagen');
            $table->string('enlace', 255)->nullable();
            $table->unsignedSmallInteger('orden')->default(0)->index();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('mensajes_contacto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('empresa', 150)->nullable();
            $table->string('email', 150);
            $table->enum('tipo', ['Empresa', 'Proveedor', 'Otro']);
            $table->text('mensaje');
            $table->string('adjunto')->nullable();
            $table->boolean('leido')->default(false)->index();
            $table->timestamps();
        });

        // Bitácora de seguridad / auditoría
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 30)->index();
            $table->string('modulo', 40)->index();
            $table->string('descripcion', 500);
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        foreach (['bitacora', 'mensajes_contacto', 'banners', 'pedido_detalles', 'pedidos',
                  'producto_imagenes', 'productos', 'clientes', 'proveedores', 'categorias'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
