<?php

namespace Tests\Feature;

use App\Mail\ComverzaMail;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Notifications\RestablecerPassword;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ComverzaTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected $seeder = DatabaseSeeder::class;

    private function admin(): User
    {
        return User::where('email', 'admin@comverza.com')->first();
    }

    private function vendedor(): User
    {
        return User::where('email', 'ventas@comverza.com')->first();
    }

    // ------------------------------------------------------------ RBAC

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/panel')->assertRedirect('/login');
    }

    public function test_login_con_credenciales_correctas_e_incorrectas(): void
    {
        $this->post('/login', ['email' => 'admin@comverza.com', 'password' => 'incorrecta'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'admin@comverza.com', 'password' => 'Comverza2026!'])->assertRedirect('/panel');
    }

    public function test_vendedor_no_accede_a_seguridad_ni_puede_eliminar(): void
    {
        $v = $this->vendedor();
        $this->actingAs($v)->get('/panel/pedidos')->assertOk();
        $this->actingAs($v)->get('/panel/usuarios')->assertForbidden();
        $this->actingAs($v)->get('/panel/roles')->assertForbidden();
        $this->actingAs($v)->get('/panel/productos/create')->assertForbidden();
        $this->actingAs($v)->delete('/panel/clientes/1')->assertForbidden();
    }

    public function test_usuario_desactivado_no_puede_entrar(): void
    {
        $u = $this->vendedor();
        $u->update(['activo' => false]);
        $this->actingAs($u)->get('/panel')->assertRedirect('/login');
    }

    public function test_crear_rol_con_permisos_y_no_eliminar_rol_en_uso(): void
    {
        $this->actingAs($this->admin())->post('/panel/roles', ['name' => 'Auditor', 'permisos' => ['dashboard.ver', 'reportes.ver']])
            ->assertRedirect('/panel/roles');
        $this->assertTrue(\Spatie\Permission\Models\Role::findByName('Auditor')->hasPermissionTo('reportes.ver'));

        $vendedor = \Spatie\Permission\Models\Role::findByName('Vendedor');
        $this->actingAs($this->admin())->delete("/panel/roles/{$vendedor->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'Vendedor']);
    }

    public function test_no_puedes_eliminarte_ni_quitar_el_ultimo_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->delete("/panel/usuarios/{$admin->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_crear_usuario_envia_bienvenida(): void
    {
        Mail::fake();
        $this->actingAs($this->admin())->post('/panel/usuarios', [
            'name' => 'Nuevo Usuario', 'email' => 'nuevo@comverza.com', 'roles' => ['Vendedor'], 'activo' => 1,
        ])->assertRedirect('/panel/usuarios');
        $this->assertTrue(User::where('email', 'nuevo@comverza.com')->first()->hasRole('Vendedor'));
        Mail::assertSent(ComverzaMail::class, fn ($m) => $m->hasTo('nuevo@comverza.com'));
    }

    public function test_recuperacion_de_contrasena_autoservicio(): void
    {
        Notification::fake();
        $this->post('/olvide-mi-contrasena', ['email' => 'ventas@comverza.com'])->assertSessionHas('success');
        $u = $this->vendedor();
        $token = null;
        Notification::assertSentTo($u, RestablecerPassword::class, function ($n) use (&$token) {
            $token = $n->token;

            return true;
        });

        // contraseña débil: rechazada
        $this->post('/restablecer-contrasena', ['token' => $token, 'email' => $u->email, 'password' => 'debil', 'password_confirmation' => 'debil'])
            ->assertSessionHasErrors('password');
        // contraseña segura: aceptada
        $this->post('/restablecer-contrasena', ['token' => $token, 'email' => $u->email, 'password' => 'NuevaClave2026', 'password_confirmation' => 'NuevaClave2026'])
            ->assertRedirect('/login');
        $this->post('/login', ['email' => $u->email, 'password' => 'NuevaClave2026'])->assertRedirect();
        $this->assertAuthenticatedAs($u);
    }

    // ------------------------------------------------------------ Validaciones CRUD

    public function test_categoria_validaciones_crear_editar_eliminar(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/panel/categorias', ['nombre' => 'Tecnología', 'color' => '#123456'])->assertSessionHasErrors('nombre');
        $this->actingAs($admin)->post('/panel/categorias', ['nombre' => 'X', 'color' => 'rojo'])->assertSessionHasErrors(['nombre', 'color']);
        $this->actingAs($admin)->post('/panel/categorias', ['nombre' => 'Herramientas', 'color' => '#123456', 'activo' => 1])->assertRedirect();

        $tec = Categoria::where('nombre', 'Tecnología')->first();
        $this->actingAs($admin)->put("/panel/categorias/{$tec->id}", ['nombre' => 'Tecnología', 'color' => '#1677ff'])
            ->assertSessionHasErrors('activo'); // no se desactiva con productos activos
        $this->actingAs($admin)->delete("/panel/categorias/{$tec->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('categorias', ['id' => $tec->id]);

        $nueva = Categoria::where('nombre', 'Herramientas')->first();
        $this->actingAs($admin)->delete("/panel/categorias/{$nueva->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('categorias', ['id' => $nueva->id]);
    }

    public function test_proveedor_valida_rfc_telefono_y_pdf(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/panel/proveedores', [
            'razon_social' => 'Prueba SA', 'rfc' => 'MAL', 'contacto' => 'Ana Pérez', 'email' => 'no-es-correo',
            'telefono' => '123', 'calificacion' => 9,
            'constancia_fiscal' => UploadedFile::fake()->create('constancia.exe', 100),
        ])->assertSessionHasErrors(['rfc', 'email', 'telefono', 'calificacion', 'constancia_fiscal']);

        $this->actingAs($admin)->post('/panel/proveedores', [
            'razon_social' => 'Prueba SA', 'rfc' => 'pru260101ab1', 'contacto' => 'Ana Pérez', 'email' => 'ana@prueba.mx',
            'telefono' => '5512345678', 'calificacion' => 4, 'activo' => 1,
            'constancia_fiscal' => UploadedFile::fake()->create('constancia.pdf', 300, 'application/pdf'),
        ])->assertRedirect();
        $p = Proveedor::where('rfc', 'PRU260101AB1')->first();
        $this->assertNotNull($p);
        $this->assertFileExists(public_path($p->constancia_fiscal));
        File::delete(public_path($p->constancia_fiscal));

        $conProductos = Proveedor::has('productos')->first();
        $this->actingAs($admin)->delete("/panel/proveedores/{$conProductos->id}")->assertSessionHas('error');
    }

    public function test_cliente_empresa_requiere_rfc_y_no_se_elimina_con_pedidos(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/panel/clientes', [
            'tipo' => 'Empresa', 'nombre' => 'Luis Mora', 'email' => 'luis@x.com', 'telefono' => '5511122233', 'limite_credito' => 0,
        ])->assertSessionHasErrors(['empresa', 'rfc']);

        $conPedidos = Cliente::has('pedidos')->first();
        $this->actingAs($admin)->delete("/panel/clientes/{$conPedidos->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('clientes', ['id' => $conPedidos->id]);
    }

    public function test_producto_con_imagenes_y_precio_menor_al_costo(): void
    {
        $admin = $this->admin();
        $base = ['sku' => 'TEST-001', 'nombre' => 'Producto de prueba', 'categoria_id' => 1, 'stock' => 10,
            'stock_minimo' => 2, 'unidad' => 'pieza', 'activo' => 1];

        $this->actingAs($admin)->post('/panel/productos', $base + ['costo' => 100, 'precio' => 50])->assertSessionHasErrors('precio');
        $this->actingAs($admin)->post('/panel/productos', $base + ['costo' => 100, 'precio' => 150,
            'imagen' => UploadedFile::fake()->create('virus.php', 10)])->assertSessionHasErrors('imagen');
        $this->actingAs($admin)->post('/panel/productos', $base + ['costo' => 100, 'precio' => 150,
            'imagen' => UploadedFile::fake()->image('chica.jpg', 100, 100)])->assertSessionHasErrors('imagen'); // muy pequeña

        $this->actingAs($admin)->post('/panel/productos', $base + ['costo' => 100, 'precio' => 150,
            'imagen' => UploadedFile::fake()->image('foto.jpg', 1600, 1200),
            'imagenes' => [UploadedFile::fake()->image('g1.png', 800, 600), UploadedFile::fake()->image('g2.png', 800, 600)],
        ])->assertRedirect();

        $p = Producto::where('sku', 'TEST-001')->first();
        $this->assertStringEndsWith('.webp', $p->imagen);
        $this->assertFileExists(public_path($p->imagen));
        [$w] = getimagesize(public_path($p->imagen));
        $this->assertLessThanOrEqual(1000, $w); // redimensionada por Intervention Image
        $this->assertCount(2, $p->imagenes);

        $this->actingAs($admin)->delete("/panel/productos/{$p->id}")->assertSessionHas('success');
        $this->assertFileDoesNotExist(public_path($p->imagen));

        $vendido = Producto::has('detalles')->first();
        $this->actingAs($admin)->delete("/panel/productos/{$vendido->id}")->assertSessionHas('error');
    }

    public function test_pedido_valida_stock_descuenta_y_devuelve_inventario(): void
    {
        Mail::fake();
        $admin = $this->admin();
        $prod = Producto::where('stock', '>', 20)->first();
        $cliente = Cliente::where('activo', true)->first();
        $datos = ['cliente_id' => $cliente->id, 'fecha' => now()->toDateString(), 'estado' => 'pendiente', 'metodo_pago' => 'tarjeta'];

        $this->actingAs($admin)->post('/panel/pedidos', $datos + ['items' => [['producto_id' => $prod->id, 'cantidad' => $prod->stock + 1]]])
            ->assertSessionHasErrors('items.0.cantidad');
        $this->actingAs($admin)->post('/panel/pedidos', $datos + ['items' => []])->assertSessionHasErrors('items');
        $this->actingAs($admin)->post('/panel/pedidos', ['fecha' => now()->addDays(3)->toDateString()] + $datos + ['items' => [['producto_id' => $prod->id, 'cantidad' => 1]]])
            ->assertSessionHasErrors('fecha');

        $stock = $prod->stock;
        $this->actingAs($admin)->post('/panel/pedidos', $datos + ['items' => [['producto_id' => $prod->id, 'cantidad' => 3]]])->assertRedirect();
        $pedido = Pedido::latest('id')->first();
        $this->assertEquals($stock - 3, $prod->fresh()->stock);
        $this->assertEquals(round($prod->precio * 3 * 1.16, 2), (float) $pedido->total);
        Mail::assertSent(ComverzaMail::class, fn ($m) => $m->hasTo($cliente->email) && count($m->adjuntos) === 1);

        $this->actingAs($admin)->delete("/panel/pedidos/{$pedido->id}")->assertSessionHas('success');
        $this->assertEquals($stock, $prod->fresh()->stock);

        $entregado = Pedido::where('estado', 'entregado')->first();
        $this->actingAs($admin)->delete("/panel/pedidos/{$entregado->id}")->assertSessionHas('error');
    }

    public function test_reportes_pdf_y_excel(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->get('/panel/reportes/ventas.pdf')->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->actingAs($admin)->get('/panel/pedidos/1/pdf')->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->actingAs($admin)->get('/panel/reportes/productos.xlsx')->assertOk();
        $this->actingAs($this->vendedor())->get('/panel/reportes/ventas.pdf')->assertForbidden();
    }

    public function test_contacto_valida_adjunto_y_envia_correos(): void
    {
        Mail::fake();
        $this->post('/contacto', ['nombre' => 'A', 'email' => 'x', 'tipo' => 'Nada', 'mensaje' => 'corto',
            'adjunto' => UploadedFile::fake()->create('x.exe', 50)])
            ->assertSessionHasErrors(['nombre', 'email', 'tipo', 'mensaje', 'adjunto']);

        $this->post('/contacto', ['nombre' => 'Andrea Ruiz', 'email' => 'andrea@empresa.com', 'tipo' => 'Empresa',
            'mensaje' => 'Quiero una demostración para mi empresa.'])->assertRedirect('/contacto');
        $this->assertDatabaseHas('mensajes_contacto', ['email' => 'andrea@empresa.com']);
        Mail::assertSent(ComverzaMail::class, 2); // aviso interno + acuse
    }

    // ------------------------------------------------------------ API

    public function test_api_crud_completo_de_categorias(): void
    {
        $this->getJson('/api/v1/categorias')->assertOk()->assertJsonStructure(['data' => [['id', 'nombre', 'productos_count']], 'total']);
        $id = $this->postJson('/api/v1/categorias', ['nombre' => 'API Test', 'color' => '#ffd533'])->assertCreated()->json('data.id');
        $this->getJson("/api/v1/categorias/{$id}")->assertOk()->assertJsonPath('data.nombre', 'API Test');
        $this->putJson("/api/v1/categorias/{$id}", ['nombre' => 'API Editada'])->assertOk()->assertJsonPath('data.nombre', 'API Editada');
        $this->deleteJson("/api/v1/categorias/{$id}")->assertOk();
        $this->getJson("/api/v1/categorias/{$id}")->assertNotFound();
        $this->deleteJson('/api/v1/categorias/1')->assertStatus(409); // tiene productos
    }

    public function test_api_productos_y_proveedores(): void
    {
        $this->getJson('/api/v1/productos?destacados=1')->assertOk()->assertJsonPath('data.0.destacado', true);
        $id = $this->postJson('/api/v1/productos', ['sku' => 'API-1', 'nombre' => 'Producto API', 'categoria_id' => 1, 'precio' => 99.5, 'stock' => 4])
            ->assertCreated()->json('data.id');
        $this->putJson("/api/v1/productos/{$id}", ['precio' => 120])->assertOk()->assertJsonPath('data.precio', 120);
        $this->deleteJson("/api/v1/productos/{$id}")->assertOk();

        $pid = $this->postJson('/api/v1/proveedores', ['razon_social' => 'Prov API', 'rfc' => 'API260101AA1', 'contacto' => 'Juan',
            'email' => 'api@prov.mx', 'telefono' => '5500000000'])->assertCreated()->json('data.id');
        $this->getJson("/api/v1/proveedores/{$pid}")->assertOk();
        $this->deleteJson("/api/v1/proveedores/{$pid}")->assertOk();
    }
}
