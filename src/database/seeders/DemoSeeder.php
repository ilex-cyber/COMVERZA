<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Bitacora;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\MensajeContacto;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);
        $faker = \Faker\Factory::create('es_MX');
        $faker->seed(2026);

        // ---------------------------------------------------------- Usuarios
        $usuarios = [
            ['Administrador COMVERZA', 'admin@comverza.com', 'Director de Tecnología', 'Administrador'],
            ['Gabriela Ortiz', 'gerente@comverza.com', 'Gerente Comercial', 'Gerente'],
            ['Luis Hernández', 'ventas@comverza.com', 'Ejecutivo de Ventas', 'Vendedor'],
            ['Mariana López', 'ventas2@comverza.com', 'Ejecutiva de Ventas', 'Vendedor'],
            ['Jorge Ramírez', 'almacen@comverza.com', 'Jefe de Almacén', 'Almacenista'],
        ];
        $vendedores = [];
        foreach ($usuarios as [$nombre, $email, $puesto, $rol]) {
            $u = User::create([
                'name' => $nombre, 'email' => $email, 'puesto' => $puesto,
                'telefono' => '55'.$faker->numerify('########'),
                'password' => 'Comverza2026!', 'email_verified_at' => now(), 'activo' => true,
            ]);
            $u->assignRole($rol);
            if (in_array($rol, ['Vendedor', 'Gerente'])) {
                $vendedores[] = $u->id;
            }
        }

        // -------------------------------------------------------- Categorías
        $categorias = [
            ['Tecnología', 'Equipo de cómputo, periféricos y accesorios.', '#1677ff'],
            ['Mobiliario de oficina', 'Escritorios, sillas y almacenamiento.', '#784cff'],
            ['Papelería', 'Consumibles y artículos de oficina.', '#ffbd1c'],
            ['Logística y empaque', 'Cajas, emplaye, etiquetas y tarimas.', '#0fb5a6'],
            ['Limpieza e higiene', 'Productos de limpieza institucional.', '#35a7ff'],
            ['Seguridad industrial', 'Equipo de protección personal.', '#ff6b4a'],
            ['Cafetería', 'Café, insumos y equipo para comedores.', '#a0632b'],
            ['Redes y telecom', 'Conectividad, cableado y telefonía.', '#2b3f8f'],
        ];
        $cats = collect($categorias)->map(fn ($c) => Categoria::create([
            'nombre' => $c[0], 'descripcion' => $c[1], 'color' => $c[2], 'activo' => true,
        ]));

        // ------------------------------------------------------- Proveedores
        $provNombres = [
            'TecnoDistribuidora del Bajío', 'Grupo Ofimueble', 'Papelera Nacional Azteca', 'EmpaquesPro Logística',
            'Higiene Total Institucional', 'Protección Industrial MX', 'Café Sierra Alta', 'ConectaRed Soluciones',
            'Suministros Corporativos del Norte', 'Importadora Pacífico Tech',
        ];
        $ciudades = ['Ciudad de México', 'Guadalajara', 'Monterrey', 'Querétaro', 'León', 'Puebla', 'Mérida', 'Tijuana'];
        $provs = collect($provNombres)->map(function ($nombre, $i) use ($faker, $ciudades) {
            return Proveedor::create([
                'razon_social' => $nombre.' S.A. de C.V.',
                'rfc' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nombre), 0, 3)).'2'.str_pad((string) (10101 + $i * 37), 5, '0').'AB'.$i,
                'contacto' => $faker->name(),
                'email' => 'contacto'.($i + 1).'@'.str($nombre)->slug()->limit(18, '').'.mx',
                'telefono' => '33'.$faker->numerify('########'),
                'direccion' => $faker->streetAddress(),
                'ciudad' => $ciudades[$i % count($ciudades)],
                'sitio_web' => 'https://www.'.str($nombre)->slug()->limit(18, '').'.mx',
                'calificacion' => [5, 4, 5, 3, 4, 5, 4, 5, 3, 4][$i],
                'activo' => $i !== 9,
            ]);
        });

        // --------------------------------------------------------- Productos
        $catalogo = [
            0 => [['Laptop empresarial 14"', 18999, 12], ['Monitor 27" 4K', 6499, 25], ['Teclado inalámbrico', 649, 80], ['Mouse ergonómico', 459, 95], ['Docking station USB-C', 2299, 30], ['Disco SSD 1 TB', 1599, 4]],
            1 => [['Silla ergonómica ejecutiva', 4899, 18], ['Escritorio ajustable eléctrico', 8999, 7], ['Archivero metálico 4 gavetas', 3299, 10], ['Mesa de juntas 8 personas', 12499, 3]],
            2 => [['Caja papel bond carta (10 paq.)', 899, 140], ['Tóner láser negro', 1299, 35], ['Carpetas de argolla (12 pzas)', 349, 60], ['Plumas gel (caja 24)', 189, 120], ['Post-it neón (paq. 12)', 159, 3]],
            3 => [['Caja corrugada 40x30x30 (25 pzas)', 529, 200], ['Rollo de emplaye 18"', 219, 150], ['Etiquetas térmicas 4x6 (500)', 389, 75], ['Tarima de plástico', 899, 40]],
            4 => [['Gel antibacterial 4 L', 279, 90], ['Toalla interdoblada (caja)', 459, 70], ['Desinfectante multiusos 20 L', 699, 2], ['Despachador de jabón', 349, 25]],
            5 => [['Casco de seguridad', 249, 60], ['Chaleco reflejante', 129, 110], ['Guantes de nitrilo (100)', 239, 85], ['Botas industriales', 1199, 22]],
            6 => [['Café de altura 1 kg', 389, 65], ['Cafetera industrial 40 tazas', 2899, 8], ['Vasos térmicos (paq. 50)', 199, 4]],
            7 => [['Switch administrable 24 puertos', 5899, 9], ['Access point Wi-Fi 6', 2499, 16], ['Bobina cable Cat6 305 m', 3199, 11], ['Teléfono IP', 1499, 20]],
        ];
        // Fotografías de demostración (public/images/productos)
        $fotos = [
            'Laptop empresarial 14"' => 'laptop', 'Monitor 27" 4K' => 'monitor', 'Teclado inalámbrico' => 'teclado',
            'Mouse ergonómico' => 'mouse', 'Docking station USB-C' => 'laptop', 'Disco SSD 1 TB' => 'servidores',
            'Silla ergonómica ejecutiva' => 'silla', 'Escritorio ajustable eléctrico' => 'escritorio',
            'Archivero metálico 4 gavetas' => 'escritorio', 'Mesa de juntas 8 personas' => 'escritorio',
            'Caja papel bond carta (10 paq.)' => 'papel', 'Tóner láser negro' => 'impresora',
            'Carpetas de argolla (12 pzas)' => 'papel', 'Plumas gel (caja 24)' => 'pluma', 'Post-it neón (paq. 12)' => 'pluma',
            'Caja corrugada 40x30x30 (25 pzas)' => 'caja', 'Rollo de emplaye 18"' => 'caja',
            'Etiquetas térmicas 4x6 (500)' => 'caja', 'Tarima de plástico' => 'caja',
            'Gel antibacterial 4 L' => 'limpieza', 'Toalla interdoblada (caja)' => 'limpieza',
            'Desinfectante multiusos 20 L' => 'limpieza', 'Despachador de jabón' => 'limpieza',
            'Casco de seguridad' => 'seguridad', 'Chaleco reflejante' => 'seguridad',
            'Guantes de nitrilo (100)' => 'guantes', 'Botas industriales' => 'seguridad',
            'Café de altura 1 kg' => 'cafe-bolsa', 'Cafetera industrial 40 tazas' => 'cafe-tazas', 'Vasos térmicos (paq. 50)' => 'cafe-tazas',
            'Switch administrable 24 puertos' => 'servidores', 'Access point Wi-Fi 6' => 'router',
            'Bobina cable Cat6 305 m' => 'servidores', 'Teléfono IP' => 'router',
        ];
        $productos = collect();
        $n = 1;
        foreach ($catalogo as $ci => $items) {
            foreach ($items as [$nombre, $precio, $stock]) {
                $productos->push(Producto::create([
                    'sku' => 'CVZ-'.str_pad((string) $n++, 4, '0', STR_PAD_LEFT),
                    'nombre' => $nombre,
                    'descripcion' => "{$nombre} para uso empresarial. Garantía y soporte directo con el proveedor.",
                    'categoria_id' => $cats[$ci]->id,
                    'proveedor_id' => $provs[$ci]->id,
                    'costo' => round($precio * 0.68, 2),
                    'precio' => $precio,
                    'stock' => $stock,
                    'stock_minimo' => 5,
                    'unidad' => 'pieza',
                    'imagen' => isset($fotos[$nombre]) ? "images/productos/{$fotos[$nombre]}.jpg" : null,
                    'destacado' => $n % 4 === 0,
                    'activo' => true,
                ]));
            }
        }

        // ---------------------------------------------------------- Clientes
        $clientes = collect();
        for ($i = 0; $i < 28; $i++) {
            $empresa = $i < 22;
            $clientes->push(Cliente::create([
                'tipo' => $empresa ? 'Empresa' : 'Persona',
                'nombre' => $faker->name(),
                'empresa' => $empresa ? $faker->company() : null,
                'rfc' => $empresa ? strtoupper($faker->bothify('???######??#')) : null,
                'email' => $faker->unique()->safeEmail(),
                'telefono' => '55'.$faker->numerify('########'),
                'direccion' => $faker->streetAddress(),
                'ciudad' => $ciudades[$i % count($ciudades)],
                'codigo_postal' => $faker->numerify('#####'),
                'limite_credito' => $empresa ? $faker->randomElement([50000, 100000, 250000]) : 0,
                'activo' => $i !== 27,
            ]));
        }

        // ----------------------------------------------- Pedidos (12 meses)
        DB::transaction(function () use ($clientes, $productos, $vendedores, $faker) {
            $folio = 1001;
            for ($m = 11; $m >= 0; $m--) {
                $inicio = Carbon::now()->startOfMonth()->subMonths($m);
                $cantidad = 9 + (11 - $m) + mt_rand(0, 4);   // tendencia de crecimiento
                for ($k = 0; $k < $cantidad; $k++) {
                    $fecha = $inicio->copy()->addDays(mt_rand(0, $m === 0 ? max(0, now()->day - 1) : 27));
                    $estado = $m >= 2
                        ? $faker->randomElement(['entregado', 'entregado', 'entregado', 'entregado', 'cancelado'])
                        : $faker->randomElement(['pendiente', 'en_proceso', 'enviado', 'entregado']);
                    $pedido = Pedido::create([
                        'folio' => 'CV-'.str_pad((string) $folio++, 5, '0', STR_PAD_LEFT),
                        'cliente_id' => $clientes->random()->id,
                        'user_id' => $faker->randomElement($vendedores),
                        'fecha' => $fecha,
                        'fecha_entrega' => $fecha->copy()->addDays(mt_rand(2, 7)),
                        'estado' => $estado,
                        'metodo_pago' => $faker->randomElement(array_keys(Pedido::METODOS_PAGO)),
                        'created_at' => $fecha, 'updated_at' => $fecha,
                    ]);
                    foreach ($productos->random(mt_rand(1, 4)) as $p) {
                        $cant = mt_rand(1, 6);
                        $pedido->detalles()->create([
                            'producto_id' => $p->id, 'cantidad' => $cant,
                            'precio_unitario' => $p->precio, 'importe' => $cant * $p->precio,
                        ]);
                    }
                    $pedido->recalcularTotales();
                }
            }
        });

        // ----------------------------------------- Fotos de la landing
        $banners = [
            ['Centraliza tu operación', 'Productos, pedidos y proveedores en un solo lugar.', 'images/landing/centro-distribucion.jpg'],
            ['Decisiones con datos reales', 'Dashboards de impacto y reportes en PDF y Excel.', 'images/landing/analitica.jpg'],
            ['Logística conectada', 'Seguimiento de pedidos desde el almacén hasta tu cliente.', 'images/landing/puerto-logistico.jpg'],
            ['Alianzas que crecen', 'Una red de proveedores confiables para tu negocio.', 'images/landing/alianza-comercial.jpg'],
            ['Equipos más productivos', 'Roles y permisos para que cada quien vea lo que necesita.', 'images/landing/equipo-colaborando.jpg'],
        ];
        foreach ($banners as $i => [$t, $s, $img]) {
            Banner::create(['titulo' => $t, 'subtitulo' => $s, 'imagen' => $img, 'orden' => $i + 1, 'activo' => true]);
        }

        MensajeContacto::create([
            'nombre' => 'Andrea Salinas', 'empresa' => 'Comercializadora del Centro', 'email' => 'andrea@example.com',
            'tipo' => 'Empresa', 'mensaje' => 'Nos interesa una demostración de la plataforma para 3 sucursales.',
        ]);
        MensajeContacto::create([
            'nombre' => 'Raúl Medina', 'empresa' => 'Distribuidora Medina', 'email' => 'raul@example.com',
            'tipo' => 'Proveedor', 'mensaje' => 'Queremos darnos de alta como proveedores de material de empaque.',
        ]);

        Bitacora::create(['user_id' => 1, 'accion' => 'sistema', 'modulo' => 'instalacion', 'descripcion' => 'Carga inicial de datos de demostración.']);
    }
}
