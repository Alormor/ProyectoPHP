document.addEventListener("DOMContentLoaded", function () {
  const filtroNombre = document.getElementById("filtro-nombre");
  const filtroCategoria = document.getElementById("filtro-categoria");

  if (!filtroNombre || !filtroCategoria) return;

  let debounceTimer;

  function navegarConFiltros() {
    const nombreFiltro = filtroNombre.value.trim();
    const categoriaFiltro = filtroCategoria.value;
    const params = new URLSearchParams(window.location.search);

    if (nombreFiltro) {
      params.set("nombre", nombreFiltro);
    } else {
      params.delete("nombre");
    }

    if (categoriaFiltro) {
      params.set("categoria", categoriaFiltro);
    } else {
      params.delete("categoria");
    }

    // Si cambian filtros, siempre volvemos a la página 1.
    params.delete("page");

    const query = params.toString();
    const urlDestino = query
      ? `${window.location.pathname}?${query}`
      : window.location.pathname;

    window.location.assign(urlDestino);
  }

  function navegarConDebounce() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(navegarConFiltros, 250);
  }

  filtroNombre.addEventListener("input", navegarConDebounce);
  filtroCategoria.addEventListener("change", navegarConFiltros);
});
