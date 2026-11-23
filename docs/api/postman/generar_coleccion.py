"""
Genera la colección de Postman de la API de COMVERZA.

    py generar_coleccion.py

Crea COMVERZA-API.postman_collection.json con, para cada una de las 3 APIs
(categorías, productos y proveedores):

    GET all · GET one · POST (create) · PUT (update) · DELETE

Cada petición incluye pruebas (tests) y el POST guarda el id creado en una
variable de colección para que PUT/GET one/DELETE lo reutilicen.
"""
import json
from pathlib import Path

RECURSOS = {
    "categorias": {
        "nombre": "Categorías",
        "crear": {"nombre": "Herramientas eléctricas", "descripcion": "Taladros, pulidoras y accesorios.", "color": "#784cff", "activo": True},
        "actualizar": {"nombre": "Herramientas eléctricas PRO", "descripcion": "Línea profesional.", "color": "#ffd533"},
    },
    "productos": {
        "nombre": "Productos",
        "crear": {"sku": "PM-{{$randomInt}}", "nombre": "Taladro inalámbrico 20V", "descripcion": "Incluye 2 baterías.",
                  "categoria_id": 1, "proveedor_id": 1, "costo": 1450, "precio": 2199, "stock": 15, "stock_minimo": 3,
                  "unidad": "pieza", "destacado": True, "activo": True},
        "actualizar": {"precio": 1999, "stock": 25},
    },
    "proveedores": {
        "nombre": "Proveedores",
        "crear": {"razon_social": "Herramientas del Pacífico S.A. de C.V.", "rfc": "HPA2601{{$randomInt}}", "contacto": "Sofía Navarro",
                  "email": "ventas{{$randomInt}}@herrapacifico.mx", "telefono": "3312345678", "ciudad": "Guadalajara",
                  "calificacion": 5, "activo": True},
        "actualizar": {"contacto": "Sofía Navarro Ruiz", "calificacion": 4},
    },
}


def peticion(nombre, metodo, ruta, cuerpo=None, tests=None):
    req = {
        "method": metodo,
        "header": [{"key": "Accept", "value": "application/json"}],
        "url": {"raw": "{{base_url}}/" + ruta, "host": ["{{base_url}}"], "path": ruta.split("/")},
    }
    if cuerpo is not None:
        req["header"].append({"key": "Content-Type", "value": "application/json"})
        req["body"] = {"mode": "raw", "raw": json.dumps(cuerpo, ensure_ascii=False, indent=2),
                       "options": {"raw": {"language": "json"}}}
    item = {"name": nombre, "request": req}
    if tests:
        item["event"] = [{"listen": "test", "script": {"type": "text/javascript", "exec": tests}}]
    return item


carpetas = []
for clave, r in RECURSOS.items():
    var = f"{clave}_id"
    carpetas.append({
        "name": f"API {r['nombre']}",
        "item": [
            peticion(f"GET all · {r['nombre']}", "GET", clave, tests=[
                "pm.test('200 OK', () => pm.response.to.have.status(200));",
                "pm.test('Devuelve una lista', () => pm.expect(pm.response.json().data).to.be.an('array'));",
            ]),
            peticion(f"POST create · {r['nombre']}", "POST", clave, r["crear"], tests=[
                "pm.test('201 Created', () => pm.response.to.have.status(201));",
                f"pm.collectionVariables.set('{var}', pm.response.json().data.id);",
            ]),
            peticion(f"GET one · {r['nombre']}", "GET", f"{clave}/{{{{{var}}}}}", tests=[
                "pm.test('200 OK', () => pm.response.to.have.status(200));",
                f"pm.test('Mismo id', () => pm.expect(pm.response.json().data.id).to.eql(Number(pm.collectionVariables.get('{var}'))));",
            ]),
            peticion(f"PUT update · {r['nombre']}", "PUT", f"{clave}/{{{{{var}}}}}", r["actualizar"], tests=[
                "pm.test('200 OK', () => pm.response.to.have.status(200));",
            ]),
            peticion(f"DELETE · {r['nombre']}", "DELETE", f"{clave}/{{{{{var}}}}}", tests=[
                "pm.test('200 OK', () => pm.response.to.have.status(200));",
            ]),
        ],
    })

coleccion = {
    "info": {
        "name": "COMVERZA API v1",
        "description": "API REST pública de COMVERZA: categorías, productos y proveedores (sin validación ni autenticación).",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
    },
    "variable": [{"key": "base_url", "value": "http://localhost:8082/api/v1"}]
                + [{"key": f"{c}_id", "value": "1"} for c in RECURSOS],
    "item": carpetas,
}

destino = Path(__file__).with_name("COMVERZA-API.postman_collection.json")
destino.write_text(json.dumps(coleccion, ensure_ascii=False, indent=2), encoding="utf-8")
print(f"Colección generada: {destino.name} ({sum(len(c['item']) for c in carpetas)} peticiones)")
