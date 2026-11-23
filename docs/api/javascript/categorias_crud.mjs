/**
 * COMVERZA · Consumo de los cuatro métodos (GET, POST, PUT, DELETE)
 * de la API de categorías con JavaScript (Node.js 18+, fetch nativo).
 *
 *   node categorias_crud.mjs
 *
 * La misma lógica corre en el navegador en el panel: /panel/consola-api
 */
const API = process.env.COMVERZA_API ?? "http://localhost:8082/api/v1/categorias";

async function llamar(metodo, url, cuerpo) {
    const res = await fetch(url, {
        method: metodo,
        headers: { Accept: "application/json", ...(cuerpo && { "Content-Type": "application/json" }) },
        body: cuerpo ? JSON.stringify(cuerpo) : undefined,
    });
    const datos = await res.json();
    console.log(`\n▶ ${metodo} ${url} → ${res.status}`);
    return datos;
}

// 1) GET (todas)
const todas = await llamar("GET", API);
console.table(todas.data.map(({ id, nombre, productos_count }) => ({ id, nombre, productos: productos_count })));

// 2) POST (crear)
const creada = await llamar("POST", API, {
    nombre: `Categoría JS ${Date.now()}`,
    descripcion: "Creada desde JavaScript con fetch()",
    color: "#784cff",
    activo: true,
});
console.log(creada.message, creada.data);
const id = creada.data.id;

// GET (una)
const una = await llamar("GET", `${API}/${id}`);
console.log(una.data);

// 3) PUT (actualizar)
const actualizada = await llamar("PUT", `${API}/${id}`, { nombre: `Categoría JS editada ${id}`, color: "#ffd533" });
console.log(actualizada.message, actualizada.data);

// 4) DELETE (eliminar)
const eliminada = await llamar("DELETE", `${API}/${id}`);
console.log(eliminada.message);
