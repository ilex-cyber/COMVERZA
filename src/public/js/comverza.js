document.addEventListener("DOMContentLoaded", () => {

    /*
    ========================================
    MENÚ MÓVIL
    ========================================
    */
    const toggle = document.querySelector("[data-nav-toggle]");
    const nav = document.querySelector("[data-nav]");
    if (toggle && nav) {
        toggle.addEventListener("click", () => nav.classList.toggle("open"));
    }

    /*
    ========================================
    CONTACTO: VALIDACIÓN DEL ARCHIVO ADJUNTO
    ========================================
    */
    const adjunto = document.getElementById("adjunto");
    const hint = document.getElementById("adjuntoHint");
    if (adjunto && hint) {
        const original = hint.textContent;
        adjunto.addEventListener("change", () => {
            const f = adjunto.files[0];
            hint.classList.remove("error");
            hint.textContent = original;
            if (!f) return;
            const ext = f.name.split(".").pop().toLowerCase();
            const max = parseFloat(adjunto.dataset.maxMb || "5");
            if (!["pdf", "jpg", "jpeg", "png", "docx"].includes(ext)) {
                adjunto.value = "";
                hint.classList.add("error");
                hint.textContent = `⚠ "${f.name}" no es un tipo permitido (PDF, JPG, PNG o DOCX).`;
            } else if (f.size > max * 1048576) {
                adjunto.value = "";
                hint.classList.add("error");
                hint.textContent = `⚠ El archivo pesa ${(f.size / 1048576).toFixed(1)} MB; el máximo es ${max} MB.`;
            } else {
                hint.textContent = `✓ ${f.name} (${Math.round(f.size / 1024)} KB)`;
            }
        });
    }

    /*
    ========================================
    ANIMACIONES AL HACER SCROLL
    ========================================
    */

    const elementos =
        document.querySelectorAll(".reveal");


    const observer =
        new IntersectionObserver(
            (entries) => {

                entries.forEach((entry) => {

                    if (entry.isIntersecting) {

                        entry.target.classList.add("visible");

                    }

                });

            },
            {
                threshold: 0.15
            }
        );


    elementos.forEach((elemento) => {

        observer.observe(elemento);

    });


    /*
    ========================================
    EFECTO PARALLAX DEL DASHBOARD
    ========================================
    */

    const visual =
        document.querySelector(".hero-visual");


    if (visual) {

        visual.addEventListener(
            "mousemove",
            (event) => {

                const rect =
                    visual.getBoundingClientRect();


                const x =
                    ((event.clientX - rect.left)
                    / rect.width) - 0.5;


                const y =
                    ((event.clientY - rect.top)
                    / rect.height) - 0.5;


                visual.style.transform =
                    `
                    translate(
                        ${x * 8}px,
                        ${y * 8}px
                    )
                    `;

            }
        );


        visual.addEventListener(
            "mouseleave",
            () => {

                visual.style.transform =
                    "translate(0,0)";

            }
        );

    }

});