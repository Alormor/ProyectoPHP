document.addEventListener("DOMContentLoaded", () => {
    const items = document.querySelectorAll('.list');
    const currentPath = window.location.pathname.toLowerCase();

    items.forEach(item => {
        item.classList.remove('active');

        const link = item.querySelector('a');
        const href = link.getAttribute("href");

        // Ignorar enlaces con #
        if (!href || href === "#") return;

        const linkPath = new URL(link.href).pathname.toLowerCase();

        if (
            (currentPath === "/" || currentPath.includes("index")) &&
            (linkPath === "/" || linkPath.includes("index"))
        ) {
            //se queda activo 
            item.classList.add('active');
            return;
        }

        if (currentPath === linkPath) {
            item.classList.add('active');
        }
    });
});