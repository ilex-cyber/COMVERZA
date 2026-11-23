"""
COMVERZA · Prueba del método GET con Python (API de productos)

Uso:
    py get_productos.py              -> GET all  /api/v1/productos
    py get_productos.py 5            -> GET one  /api/v1/productos/5

Solo usa la biblioteca estándar (urllib), no requiere instalar nada.
"""
import json
import sys
import time
import urllib.error
import urllib.request

sys.stdout.reconfigure(encoding="utf-8")  # acentos correctos en la consola de Windows

BASE_URL = "http://localhost:8082/api/v1/productos"


def get(url: str):
    req = urllib.request.Request(url, headers={"Accept": "application/json"})
    inicio = time.perf_counter()
    try:
        with urllib.request.urlopen(req, timeout=10) as r:
            cuerpo = json.loads(r.read().decode("utf-8"))
            return r.status, cuerpo, (time.perf_counter() - inicio) * 1000
    except urllib.error.HTTPError as e:
        return e.code, json.loads(e.read().decode("utf-8")), (time.perf_counter() - inicio) * 1000


def main():
    if len(sys.argv) > 1:
        url = f"{BASE_URL}/{sys.argv[1]}"
        status, datos, ms = get(url)
        print(f"GET {url} -> {status} ({ms:.0f} ms)\n")
        print(json.dumps(datos, ensure_ascii=False, indent=2))
        return

    status, datos, ms = get(BASE_URL)
    print(f"GET {BASE_URL} -> {status} ({ms:.0f} ms)")
    print(f"Total de productos: {datos['total']}\n")
    print(f"{'ID':>3}  {'SKU':<9} {'PRODUCTO':<36} {'CATEGORÍA':<22} {'PRECIO':>11} {'STOCK':>6}")
    print("-" * 94)
    for p in datos["data"]:
        print(f"{p['id']:>3}  {p['sku']:<9} {p['nombre'][:35]:<36} {p['categoria']['nombre'][:21]:<22} "
              f"${p['precio']:>10,.2f} {p['stock']:>6}")

    valor = sum(p["precio"] * p["stock"] for p in datos["data"])
    agotados = [p["nombre"] for p in datos["data"] if p["stock"] == 0]
    print("-" * 94)
    print(f"Valor del inventario a precio de venta: ${valor:,.2f} MXN")
    print(f"Productos agotados: {len(agotados)}")


if __name__ == "__main__":
    main()
