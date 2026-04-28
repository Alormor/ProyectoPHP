<div class="productos-container">
    <h1><?php echo $title; ?></h1>
    <p><?php echo $message; ?></p>
    
    <div class="filtro-productos">
        <input type="text" id="filtro-nombre" placeholder="Filtrar por nombre...">
        <select id="filtro-categoria">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo htmlspecialchars($categoria['id']); ?>">
                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php if (empty($productos)): ?>
        <p>No hay productos disponibles en este momento.</p>
    <?php else: ?>
        <div class="productos-grid" id="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card" data-categoria="<?php echo htmlspecialchars($producto['categoria_id']); ?>" data-nombre="<?php echo htmlspecialchars(strtolower($producto['nombre'])); ?>">
                    <?php if (!empty($producto['imagen'])): ?>
                        <div class="producto-imagen">
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>"
                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="producto-imagen sin-imagen">
                            <p>Sin imagen</p>
                        </div>
                    <?php endif; ?>

                    <div class="producto-info">
                        <h2 class="producto-nombre"><?php echo htmlspecialchars($producto['nombre']); ?></h2>

                        <?php if (!empty($producto['descripcion'])): ?>
                            <p class="producto-descripcion">
                                <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 100)); ?>
                                <?php if (strlen($producto['descripcion']) > 100): ?>
                                    ...
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <div class="producto-precio">
                            <?php if (!empty($producto['precio_oferta'])): ?>
                                <span class="precio-original">
                                    $<?php echo number_format($producto['precio'], 2); ?>
                                </span>
                                <span class="precio-oferta">
                                    $<?php echo number_format($producto['precio_oferta'], 2); ?>
                                </span>
                            <?php else: ?>
                                <span class="precio">
                                    $<?php echo number_format($producto['precio'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="producto-stock">
                            <?php if ($producto['stock'] > 0): ?>
                                <span class="stock-disponible">
                                    Stock: <?php echo $producto['stock']; ?>
                                </span>
                            <?php else: ?>
                                <span class="stock-agotado">
                                    Agotado
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="producto-acciones">
                            <a href="<?php echo $_ENV['BASE_URL']; ?>/productos/<?php echo $producto['id']; ?>"
                               class="btn-ver-detalles">Ver Detalles</a>
                            <?php if ($producto['stock'] > 0): ?>
                                <button class="btn-agregar-carrito">Agregar al Carrito</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroNombre = document.getElementById('filtro-nombre');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const productosGrid = document.getElementById('productos-grid');
    
    if (!productosGrid) return;
    
    const productosCards = productosGrid.querySelectorAll('.producto-card');
    
    function aplicarFiltros() {
        const nombreFiltro = filtroNombre.value.toLowerCase();
        const categoriaFiltro = filtroCategoria.value;
        
        //Recojo por cada producto el nombre y la categoria, en realidad todo esta 
        //bloqueo las cards de los productos no hago ninguna consulta sql.
        productosCards.forEach(card => {
            const nombre = card.getAttribute('data-nombre').toLocaleLowerCase();
            const categoria = card.getAttribute('data-categoria');
            
            const coincideNombre = nombre.includes(nombreFiltro.toLowerCase());
            const coincideCategoria = !categoriaFiltro || categoria === categoriaFiltro;
            
            if (coincideNombre && coincideCategoria) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    filtroNombre.addEventListener('input', aplicarFiltros);
    filtroCategoria.addEventListener('change', aplicarFiltros);
});
</script>