document.addEventListener("DOMContentLoaded", function () {
  const filtroNombre = document.getElementById("filtro-nombre");
  const filtroCategoria = document.getElementById("filtro-categoria");
  const productosGrid = document.getElementById("productos-grid");

  if (!productosGrid) return;

  const productosCards = productosGrid.querySelectorAll(".producto-card");

  function aplicarFiltros() {
    const nombreFiltro = filtroNombre.value.toLowerCase();
    const categoriaFiltro = filtroCategoria.value;

    //Recojo por cada producto el nombre y la categoria, en realidad todo esta
    //bloqueo las cards de los productos no hago ninguna consulta sql.
    productosCards.forEach((card) => {
      const nombre = card.getAttribute("data-nombre").toLocaleLowerCase();
      const categoria = card.getAttribute("data-categoria");

      const coincideNombre = nombre.includes(nombreFiltro.toLowerCase());
      const coincideCategoria =
        !categoriaFiltro || categoria === categoriaFiltro;

      if (coincideNombre && coincideCategoria) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  }

  filtroNombre.addEventListener("input", aplicarFiltros);
  filtroCategoria.addEventListener("change", aplicarFiltros);
});
