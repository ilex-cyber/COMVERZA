/* ==========================================================
   COMVERZA · Panel
   - Menú lateral y menú de usuario
   - Confirmaciones de eliminación con SweetAlert2
   - Validación del lado del cliente (antes de enviar al servidor)
   - Captura, validación y vista previa de archivos (drag & drop)
========================================================== */
(function () {
    "use strict";

    const $ = (s, c = document) => c.querySelector(s);
    const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

    const Toast = window.Swal ? Swal.mixin({
        toast: true, position: "top-end", showConfirmButton: false, timer: 3500, timerProgressBar: true,
    }) : null;

    document.addEventListener("DOMContentLoaded", () => {
        if (window.lucide) lucide.createIcons();

        /* ---------- Menús ---------- */
        $$("[data-toggle-sidebar]").forEach(b => b.addEventListener("click", () => $("#sidebar").classList.toggle("open")));
        const chip = $("[data-user-menu]");
        if (chip) {
            chip.addEventListener("click", e => { e.stopPropagation(); chip.classList.toggle("open"); });
            document.addEventListener("click", () => chip.classList.remove("open"));
        }

        /* ---------- Mensajes flash ---------- */
        const flash = window.COMVERZA_FLASH || {};
        if (Toast && flash.success) Toast.fire({ icon: "success", title: flash.success });
        if (window.Swal && flash.error) Swal.fire({ icon: "error", title: "No se pudo completar", text: flash.error, confirmButtonColor: "#1677ff" });

        /* ---------- Confirmación de eliminación ---------- */
        $$("form.js-confirm").forEach(form => {
            form.addEventListener("submit", e => {
                if (form.dataset.confirmed) return;
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: form.dataset.titulo || "¿Eliminar este registro?",
                    html: form.dataset.mensaje || "Esta acción no se puede deshacer.",
                    showCancelButton: true,
                    confirmButtonText: form.dataset.boton || "Sí, eliminar",
                    cancelButtonText: "Cancelar",
                    confirmButtonColor: "#f04438",
                    cancelButtonColor: "#7183a0",
                    reverseButtons: true,
                    focusCancel: true,
                }).then(r => {
                    if (r.isConfirmed) { form.dataset.confirmed = "1"; form.submit(); }
                });
            });
        });

        /* ---------- Contador de caracteres ---------- */
        $$("[data-maxlength-counter]").forEach(el => {
            const max = el.getAttribute("maxlength");
            const counter = document.createElement("span");
            counter.className = "char-count";
            const label = el.closest(".field")?.querySelector("label");
            label && label.appendChild(counter);
            const upd = () => counter.textContent = `${el.value.length}/${max}`;
            el.addEventListener("input", upd); upd();
        });

        /* ---------- Validación en cliente ---------- */
        const mensajes = el => {
            const v = el.validity;
            const nombre = el.dataset.nombre || "Este campo";
            if (v.valueMissing) return `${nombre} es obligatorio.`;
            if (v.typeMismatch && el.type === "email") return "Escribe un correo electrónico válido.";
            if (v.typeMismatch && el.type === "url") return "Escribe una URL válida (https://...).";
            if (v.tooShort) return `${nombre} debe tener al menos ${el.minLength} caracteres.`;
            if (v.tooLong) return `${nombre} no debe exceder ${el.maxLength} caracteres.`;
            if (v.rangeUnderflow) return `${nombre} debe ser al menos ${el.min}.`;
            if (v.rangeOverflow) return `${nombre} no debe ser mayor que ${el.max}.`;
            if (v.stepMismatch) return `${nombre} tiene un formato numérico no válido.`;
            if (v.patternMismatch) return el.dataset.patron || `El formato de ${nombre.toLowerCase()} no es válido.`;
            if (v.customError) return el.validationMessage;
            return "";
        };

        const pintar = el => {
            const field = el.closest(".field");
            if (!field) return true;
            let box = field.querySelector(".error.js");
            const msg = el.checkValidity() ? "" : mensajes(el);
            field.classList.toggle("has-error", !!msg);
            field.classList.toggle("is-valid", !msg && el.value !== "" && el.type !== "file");
            if (msg) {
                if (!box) { box = document.createElement("div"); box.className = "error js"; field.appendChild(box); }
                box.textContent = "⚠ " + msg;
                field.querySelectorAll(".error:not(.js)").forEach(x => x.remove());
            } else if (box) box.remove();
            return !msg;
        };

        $$("form.js-validate").forEach(form => {
            form.setAttribute("novalidate", "novalidate");
            const campos = () => $$("input, select, textarea", form).filter(el => el.name && el.type !== "hidden");
            campos().forEach(el => {
                el.addEventListener("blur", () => pintar(el));
                el.addEventListener("input", () => el.closest(".field")?.classList.contains("has-error") && pintar(el));
            });
            form.addEventListener("submit", e => {
                // confirmación de contraseñas
                const pw = form.querySelector("[name=password]"), pc = form.querySelector("[name=password_confirmation]");
                if (pw && pc) pc.setCustomValidity(pw.value !== pc.value ? "Las contraseñas no coinciden." : "");
                let ok = true;
                campos().forEach(el => { if (!pintar(el)) ok = false; });
                if (form.querySelector(".dropzone.has-error")) ok = false;
                if (!ok) {
                    e.preventDefault();
                    const primero = form.querySelector(".has-error input, .has-error select, .has-error textarea, .dropzone.has-error");
                    primero && primero.scrollIntoView({ behavior: "smooth", block: "center" });
                    Toast && Toast.fire({ icon: "error", title: "Corrige los campos marcados en rojo." });
                    return;
                }
                const btn = form.querySelector("[type=submit]");
                if (btn) { btn.disabled = true; btn.innerHTML = "Guardando…"; }
            });
        });

        /* ---------- Carga de archivos: validación + vista previa ---------- */
        const fmtSize = b => b > 1048576 ? (b / 1048576).toFixed(1) + " MB" : Math.round(b / 1024) + " KB";

        $$(".dropzone").forEach(zone => {
            const input = $("input[type=file]", zone);
            const previews = zone.parentElement.querySelector(".previews");
            const maxMb = parseFloat(input.dataset.maxMb || "2");
            const maxFiles = parseInt(input.dataset.maxFiles || (input.multiple ? "6" : "1"), 10);
            const tipos = (input.getAttribute("accept") || "").split(",").map(t => t.trim()).filter(Boolean);
            const minW = parseInt(input.dataset.minWidth || "0", 10);
            let errorBox = zone.parentElement.querySelector(".error.file-js");

            const setError = msg => {
                zone.classList.toggle("has-error", !!msg);
                if (msg) {
                    if (!errorBox) { errorBox = document.createElement("div"); errorBox.className = "error file-js"; zone.after(errorBox); }
                    errorBox.textContent = "⚠ " + msg;
                } else if (errorBox) { errorBox.remove(); errorBox = null; }
            };

            ["dragenter", "dragover"].forEach(ev => zone.addEventListener(ev, () => zone.classList.add("dragover")));
            ["dragleave", "drop"].forEach(ev => zone.addEventListener(ev, () => zone.classList.remove("dragover")));

            const tipoValido = f => !tipos.length || tipos.some(t =>
                t.startsWith(".") ? f.name.toLowerCase().endsWith(t) : t.endsWith("/*") ? f.type.startsWith(t.slice(0, -1)) : f.type === t);

            const medir = f => new Promise(res => {
                if (!f.type.startsWith("image/") || !minW) return res(null);
                const img = new Image();
                img.onload = () => { res({ w: img.width, h: img.height }); URL.revokeObjectURL(img.src); };
                img.onerror = () => res(null);
                img.src = URL.createObjectURL(f);
            });

            input.addEventListener("change", async () => {
                const files = Array.from(input.files);
                if (previews) previews.innerHTML = "";
                if (!files.length) return setError("");

                if (files.length > maxFiles) { input.value = ""; return setError(`Puedes subir máximo ${maxFiles} archivo(s).`); }

                for (const f of files) {
                    if (!tipoValido(f)) { input.value = ""; previews && (previews.innerHTML = ""); return setError(`"${f.name}" no es un tipo de archivo permitido (${input.dataset.tiposTexto || tipos.join(", ")}).`); }
                    if (f.size > maxMb * 1048576) { input.value = ""; previews && (previews.innerHTML = ""); return setError(`"${f.name}" pesa ${fmtSize(f.size)}; el máximo es ${maxMb} MB.`); }
                    const dim = await medir(f);
                    if (dim && dim.w < minW) { input.value = ""; previews && (previews.innerHTML = ""); return setError(`"${f.name}" mide ${dim.w}px de ancho; el mínimo es ${minW}px.`); }

                    if (previews) {
                        const box = document.createElement("div");
                        box.className = "preview";
                        if (f.type.startsWith("image/")) {
                            const img = document.createElement("img");
                            img.src = URL.createObjectURL(f);
                            box.appendChild(img);
                        } else {
                            box.innerHTML = `<div class="file-doc"><div><b>${f.name.split(".").pop().toUpperCase()}</b>${f.name}</div></div>`;
                        }
                        box.insertAdjacentHTML("beforeend", `<span class="size">${fmtSize(f.size)}</span>`);
                        previews.appendChild(box);
                    }
                }
                setError("");
                const label = $("strong", zone);
                if (label) label.textContent = files.length === 1 ? files[0].name : `${files.length} archivos seleccionados`;
            });
        });

        /* ---------- Contraseñas: mostrar/ocultar y medidor ---------- */
        $$("[data-toggle-password]").forEach(btn => btn.addEventListener("click", () => {
            const input = btn.parentElement.querySelector("input");
            input.type = input.type === "password" ? "text" : "password";
            btn.textContent = input.type === "password" ? "Ver" : "Ocultar";
        }));
        $$("[data-password-meter]").forEach(input => {
            const bar = input.closest(".field").querySelector(".pw-meter span");
            const hint = input.closest(".field").querySelector(".hint");
            input.addEventListener("input", () => {
                const v = input.value; let s = 0;
                if (v.length >= 8) s++; if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++; if (/\d/.test(v)) s++; if (/[^\w]/.test(v)) s++;
                const c = ["#f04438", "#f79009", "#ffd533", "#35a7ff", "#12b76a"][s];
                bar.style.width = (s / 4 * 100) + "%"; bar.style.background = c;
                if (hint) hint.textContent = ["Muy débil", "Débil", "Aceptable", "Buena", "Excelente"][s] + " · mínimo 8 caracteres, mayúsculas, minúsculas y números.";
            });
        });

        /* ---------- Autoenvío de filtros ---------- */
        $$("form.js-filtros select").forEach(s => s.addEventListener("change", () => s.form.submit()));
    });
})();
