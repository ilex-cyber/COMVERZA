/* ==========================================================
   COMVERZA · Landing
   Consume los endpoints GET de la API pública para mostrar al
   visitante información viva: categorías, productos destacados,
   proveedores e indicadores. Además inicia el carrusel (Swiper).
========================================================== */
(function () {
    "use strict";

    const API = window.COMVERZA_API || "/api/v1";
    const CATALOGO = window.COMVERZA_CATALOGO || "/catalogo";
    const mxn = v => Number(v).toLocaleString("es-MX", { style: "currency", currency: "MXN" });
    const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
    const getJSON = url => fetch(url, { headers: { Accept: "application/json" } }).then(r => { if (!r.ok) throw new Error(r.status); return r.json(); });

    const error = (el, msg) => { if (el) el.innerHTML = `<div class="api-error">⚠ ${msg}</div>`; };

    const animarConteo = (el, fin) => {
        const t0 = performance.now(), dur = 900;
        const paso = t => { const p = Math.min(1, (t - t0) / dur); el.textContent = Math.round(fin * (1 - Math.pow(1 - p, 3))) + (p === 1 ? "+" : ""); if (p < 1) requestAnimationFrame(paso); };
        requestAnimationFrame(paso);
    };

    document.addEventListener("DOMContentLoaded", () => {
        /* ---------- Carrusel de fotos ---------- */
        if (window.Swiper && document.querySelector(".showcase-swiper")) {
            new Swiper(".showcase-swiper", {
                loop: true, speed: 800, grabCursor: true,
                autoplay: { delay: 4500, disableOnInteraction: false, pauseOnMouseEnter: true },
                effect: "creative",
                creativeEffect: { prev: { translate: ["-20%", 0, -1], opacity: 0 }, next: { translate: ["100%", 0, 0] } },
                pagination: { el: ".swiper-pagination", clickable: true },
                navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            });
        }

        /* ---------- GET /categorias ---------- */
        const elCat = document.getElementById("apiCategorias");
        getJSON(`${API}/categorias?activas=1`).then(({ data, total }) => {
            document.querySelectorAll('[data-api-count="categorias"]').forEach(e => animarConteo(e, total));
            if (!elCat) return;
            elCat.innerHTML = data.map(c => `
                <a class="category-card" href="${CATALOGO}?categoria=${c.id}" style="--cat:${esc(c.color)}">
                    <span class="category-dot">${esc(c.nombre).charAt(0)}</span>
                    <strong>${esc(c.nombre)}</strong>
                    <small>${esc(c.descripcion || "")}</small>
                    <em>${c.productos_count} producto${c.productos_count === 1 ? "" : "s"} →</em>
                </a>`).join("");
        }).catch(() => error(elCat, "No pudimos cargar las categorías en este momento."));

        /* ---------- GET /productos?destacados=1 ---------- */
        const elProd = document.getElementById("apiProductos");
        getJSON(`${API}/productos?activos=1`).then(({ data, total }) => {
            document.querySelectorAll('[data-api-count="productos"]').forEach(e => animarConteo(e, total));
            if (!elProd) return;
            let lista = data.filter(p => p.destacado);
            if (lista.length < 8) lista = lista.concat(data.filter(p => !p.destacado)).slice(0, 8);
            elProd.innerHTML = lista.slice(0, 8).map(tarjetaProducto).join("");
        }).catch(() => error(elProd, "No pudimos cargar los productos en este momento."));

        /* ---------- GET /proveedores ---------- */
        const elProv = document.getElementById("apiProveedores");
        getJSON(`${API}/proveedores?activos=1`).then(({ data, total }) => {
            document.querySelectorAll('[data-api-count="proveedores"]').forEach(e => animarConteo(e, total));
            if (!elProv) return;
            elProv.innerHTML = data.slice(0, 6).map(p => `
                <article class="provider-card">
                    <div class="provider-avatar">${esc(p.razon_social).split(" ").slice(0, 2).map(w => w[0]).join("")}</div>
                    <div>
                        <strong>${esc(p.razon_social.replace(/ S\.A\. de C\.V\./, ""))}</strong>
                        <small>📍 ${esc(p.ciudad || "México")} · ${p.productos_count} productos</small>
                        <span class="stars">${"★".repeat(p.calificacion)}<i>${"★".repeat(5 - p.calificacion)}</i></span>
                    </div>
                </article>`).join("");
        }).catch(() => error(elProv, "No pudimos cargar a los proveedores en este momento."));
    });

    function tarjetaProducto(p) {
        const disp = p.stock > 10 ? ["ok", "Disponible"] : p.stock > 0 ? ["low", `Últimas ${p.stock} piezas`] : ["out", "Agotado"];
        return `
            <article class="product-card">
                <div class="product-media">
                    <img src="${esc(p.imagen)}" alt="${esc(p.nombre)}" loading="lazy">
                    ${p.destacado ? '<span class="product-flag">Destacado</span>' : ""}
                </div>
                <div class="product-body">
                    <span class="product-cat" style="--cat:${esc(p.categoria?.color || "#1677ff")}">${esc(p.categoria?.nombre || "")}</span>
                    <h3>${esc(p.nombre)}</h3>
                    <small class="product-sku">${esc(p.sku)}</small>
                    <div class="product-foot">
                        <strong>${mxn(p.precio)}</strong>
                        <span class="stock ${disp[0]}">${disp[1]}</span>
                    </div>
                </div>
            </article>`;
    }
    window.COMVERZA_tarjetaProducto = tarjetaProducto;
})();
