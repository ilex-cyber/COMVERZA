document.addEventListener("DOMContentLoaded", () => {

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