# COMVERZA · Business Platform

Plataforma web B2B que conecta empresas, clientes y proveedores: catálogo, inventario, pedidos, reportes, dashboard de impacto, seguridad por roles (RBAC) y una API REST pública.

**Stack:** Laravel 13 · PHP 8.3 (Apache) · MariaDB 11 · Mailpit · Docker Compose

---

## 1. Puesta en marcha

Requisito: Docker Desktop.

```bash
docker compose up -d --build
```

El primer arranque instala dependencias, crea las tablas y carga datos de demostración (tarda 1–2 min). Después:

| Servicio | URL |
|---|---|
| Sitio público | http://localhost:8082 |
| Panel administrativo | http://localhost:8082/login |
| Bandeja de correos (Mailpit) | http://localhost:8025 |
| API REST | http://localhost:8082/api/v1 |
| MariaDB (cliente externo) | `localhost:63307` · usuario `comverza` / `comverza_password` |

### Cuentas de demostración (contraseña `Comverza2026!`)

| Correo | Rol |
|---|---|
| admin@comverza.com | Administrador (acceso total) |
| gerente@comverza.com | Gerente |
| ventas@comverza.com | Vendedor |
| almacen@comverza.com | Almacenista |

Comandos útiles:

```bash
docker exec comverza-laravel php artisan migrate:fresh --seed   # reiniciar la base con datos demo
```

```bash
docker exec comverza-laravel php artisan test                   # 19 pruebas automatizadas
```

---

## 2. Base de datos

Migraciones en `src/database/migrations`. Tablas de negocio:

| Tabla | Relaciones / reglas |
|---|---|
| `categorias` | nombre único, color, imagen |
| `proveedores` | RFC y correo únicos, logo, constancia fiscal (PDF) |
| `clientes` | tipo Empresa/Persona, RFC y correo únicos, límite de crédito |
| `productos` | SKU único · FK `categoria_id` y `proveedor_id` (RESTRICT) · imagen, ficha técnica (PDF) |
| `producto_imagenes` | galería por producto (CASCADE) |
| `pedidos` / `pedido_detalles` | FK a cliente (RESTRICT), vendedor, detalle con FK a producto · subtotal, IVA 16 %, total |
| `banners` | fotos del carrusel de la landing |
| `mensajes_contacto` | formulario público con adjunto |
| `bitacora` | auditoría: logins, intentos fallidos, altas, bajas, exportaciones |
| `users`, `roles`, `permissions`, … | RBAC (spatie/laravel-permission) |

Datos de demostración: 5 usuarios, 8 categorías, 10 proveedores, 34 productos con foto, 28 clientes y unos 190 pedidos repartidos en los últimos 12 meses.

---

## 3. Validaciones de crear, editar y eliminar

Cada formulario se valida **dos veces**: en el navegador (antes de enviar, con mensajes en rojo junto al campo) y en el servidor (reglas de Laravel con mensajes en español en `src/lang/es`).

| Módulo | Crear / editar | Eliminar |
|---|---|---|
| Categorías | nombre único 3–80, color hexadecimal, imagen ≥ 200 px | bloqueado si tiene productos; no se desactiva si tiene productos activos |
| Proveedores | RFC con formato SAT, teléfono de 10 dígitos, correo único, URL válida, logo (imagen) y constancia (solo PDF ≤ 5 MB) | bloqueado si surte productos |
| Clientes | empresa y RFC obligatorios si es tipo Empresa, nombre solo letras, CP de 5 dígitos | bloqueado si tiene pedidos |
| Productos | SKU único, categoría y proveedor activos, **precio ≥ costo**, imagen ≥ 300 px, galería de hasta 6 imágenes, ficha técnica PDF | bloqueado si aparece en pedidos |
| Pedidos | cliente activo, fecha no futura, entrega ≥ fecha, sin productos repetidos, **stock suficiente** | solo pendientes, en proceso o cancelados; devuelve el stock |
| Fotos de la landing | foto ≥ 1000 × 500 px, ≤ 4 MB | siempre debe quedar al menos una activa |
| Usuarios | correo único, contraseña segura, al menos un rol | no puedes eliminarte ni dejar al sistema sin Administrador |
| Roles / Permisos | nombre único, formato `modulo.accion` | el rol Administrador y los permisos del sistema están protegidos; no se elimina un rol o permiso en uso |
| Contacto (público) | nombre, correo, mensaje de 10–2000 caracteres, adjunto PDF/JPG/PNG/DOCX ≤ 5 MB | — |

Las eliminaciones piden confirmación con SweetAlert2.

### Captura, validación y carga de archivos
Componente `x-archivo`: arrastrar y soltar, vista previa, validación en el navegador (tipo, tamaño, dimensiones mínimas, número de archivos) y en el servidor. Las imágenes se procesan con **Intervention Image**: se reducen a un máximo de 1000–1800 px y se convierten a WebP. Los archivos se guardan en `public/uploads/`.

---

## 4. Seguridad RBAC

- **Usuarios, roles y permisos** con CRUD completo (`/panel/usuarios`, `/panel/roles`, `/panel/permisos`).
- 42 permisos con formato `modulo.accion` (ver, crear, editar, eliminar, exportar) y 4 roles base.
- **Asignación y validación**: middleware `permission:` en cada ruta y acción. El menú solo muestra lo permitido, los botones se ocultan con `@can`, y un acceso directo por URL devuelve la página 403.
- Usuarios desactivados se desconectan de inmediato. El login se limita a 6 intentos por minuto.
- **Recuperación de contraseña autogestiva**: `/olvide-mi-contrasena` → correo con enlace firmado (vence en 60 min) → nueva contraseña segura. Por seguridad no revela si el correo existe.
- Al crear un usuario sin contraseña, recibe un correo de bienvenida para definirla él mismo.
- Bitácora de seguridad en `/panel/bitacora`.

---

## 5. Gráficas y dashboard de impacto

- **Creación manual** (`/panel/graficas`): el usuario escribe etiquetas y valores, elige el tipo (barras, líneas, pastel, dona, radar, área polar) y la descarga en PNG. También puede cargar datos reales del sistema.
- **Dashboard de impacto** (`/panel`): ventas del mes contra el mes anterior, ticket promedio, alertas de stock, ventas y pedidos de 12 meses, ventas por categoría, pedidos por estado, desempeño por vendedor, inventario por categoría, productos más vendidos y mejores clientes.

## 6. Correos y PDF

| Correo | Cuándo se envía |
|---|---|
| Recuperación de contraseña | autoservicio |
| Bienvenida | al crear un usuario |
| Confirmación de pedido **con PDF adjunto** | al crear un pedido o con el botón «Enviar al cliente» |
| Alerta de stock bajo | cuando un pedido deja productos bajo el mínimo |
| Aviso interno y acuse al remitente | formulario de contacto |
| Reporte ejecutivo de ventas **con PDF adjunto** | botón «Enviar por correo» del dashboard |

Para enviar a buzones reales, cambia las variables `MAIL_*` en `src/.env` (instrucciones para Gmail en `.env.example`).

PDF (DomPDF): nota de pedido, inventario de productos y reporte ejecutivo de ventas.

---

## 7. Librerías integradas

| Tipo | Librería | Uso |
|---|---|---|
| Obligatoria · Gráficas | **Chart.js 4** | dashboard y constructor de gráficas |
| Obligatoria · Correos | **Laravel Mail + Mailpit** | 6 tipos de correo con plantilla de la marca |
| Obligatoria · PDF | **barryvdh/laravel-dompdf** | pedidos, inventario y reporte de ventas |
| Adicional 1 | **spatie/laravel-permission** | RBAC: roles, permisos y middleware |
| Adicional 2 | **intervention/image** | redimensionar y convertir a WebP las imágenes subidas |
| Adicional 3 | **phpoffice/phpspreadsheet** | exportar productos, clientes, proveedores y pedidos a Excel |
| Adicional 4 | **endroid/qr-code** | código QR por producto (etiquetas de almacén) |
| Adicional 5 | **SweetAlert2** | confirmaciones de eliminación, avisos y formularios emergentes |
| Adicional 6 | **Swiper** | carrusel de fotos de la landing |
| Adicional 7 | **Lucide** | íconos del panel |

---

## 8. API REST (3 procesos sin validación)

Base: `http://localhost:8082/api/v1` · sin autenticación ni validación · respuestas JSON.

| Método | Categorías | Productos | Proveedores |
|---|---|---|---|
| GET (all) | `/categorias` | `/productos` | `/proveedores` |
| GET (one) | `/categorias/{id}` | `/productos/{id}` | `/proveedores/{id}` |
| POST (create) | `/categorias` | `/productos` | `/proveedores` |
| PUT (update) | `/categorias/{id}` | `/productos/{id}` | `/proveedores/{id}` |
| DELETE | `/categorias/{id}` | `/productos/{id}` | `/proveedores/{id}` |

Filtros opcionales: `/productos?categoria=1&destacados=1&activos=1&buscar=silla&limite=8`, `/categorias?activas=1`, `/proveedores?activos=1`.

### Evidencias solicitadas — archivos y cómo ejecutarlas

| # | Entregable | Archivo | Cómo ejecutarlo |
|---|---|---|---|
| 1 | Postman: GET all/one, POST, PUT y DELETE de las 3 APIs | `docs/api/postman/COMVERZA-API.postman_collection.json` | Postman → Import → «Run collection» (15 peticiones con pruebas automáticas) |
| 2 | Python: GET de una API | `docs/api/python/get_productos.py` | `py docs/api/python/get_productos.py` y `py docs/api/python/get_productos.py 3` |
| 3 | JavaScript: los 4 métodos de una API | `docs/api/javascript/categorias_crud.mjs` y la página `/panel/consola-api` | `node docs/api/javascript/categorias_crud.mjs` o los botones de la consola web |
| 4 | Otro lenguaje (Java): POST create | `docs/api/java/CrearProveedor.java` | `java docs/api/java/CrearProveedor.java` |
| 5 | Consumo público de GET | `src/public/js/landing-api.js`, `src/resources/views/pages/catalogo.blade.php` | abrir http://localhost:8082 y http://localhost:8082/catalogo |

### 5. Justificación de los endpoints GET en la página pública

| Endpoint | Dónde se ve | Valor para el visitante |
|---|---|---|
| `GET /productos?activos=1` | «Productos destacados» en la landing y catálogo completo con búsqueda | Ve precios y existencias **reales y actualizadas** (Disponible, Últimas piezas, Agotado) sin pedir una cotización: genera confianza y acorta la venta. |
| `GET /categorias?activas=1` | «Explora por categoría» y filtros del catálogo | Ubica rápido lo que necesita su empresa y sabe cuántos productos hay en cada línea. |
| `GET /proveedores?activos=1` | «Red de proveedores» (fondo oscuro) | Muestra respaldo comercial: proveedores verificados, ciudad y calificación, lo que refuerza la credibilidad de COMVERZA. |
| Totales de las 3 APIs | Indicadores bajo el hero (34+ productos, 8+ categorías, 9+ proveedores) | Cifras vivas que dimensionan la oferta, sin cifras inventadas. |

Al cargar se muestra un esqueleto animado; si la API falla, aparece un mensaje claro en lugar de una sección vacía. Los datos sensibles (costos, márgenes, clientes, pedidos) **no** se exponen al público.
