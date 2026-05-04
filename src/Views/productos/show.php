<div class="producto-detalle-container">
    <a class="producto-detalle-volver" href="<?php echo $_ENV['BASE_URL']; ?>/productos">
        <ion-icon name="arrow-back-outline"></ion-icon>
        Volver al catálogo
    </a>

    <section class="producto-detalle">
        <div class="producto-detalle-media">
            <?php if (!empty($producto['imagen'])): ?>
                <img
                    src="<?php echo htmlspecialchars($producto['imagen']); ?>"
                    alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                >
            <?php else: ?>
                <div class="producto-detalle-sin-imagen">
                    <ion-icon name="image-outline"></ion-icon>
                    <span>Sin imagen disponible</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="producto-detalle-content">
            <span class="producto-detalle-kicker">Ficha de producto</span>
            <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>

            <div class="producto-detalle-meta">
                <span class="producto-detalle-categoria">
                    <ion-icon name="pricetags-outline"></ion-icon>
                    <?php echo htmlspecialchars($categoria['nombre'] ?? ('Categoría #' . ($producto['categoria_id'] ?? 'N/A'))); ?>
                </span>
                <span class="producto-detalle-stock <?php echo ($producto['stock'] > 0) ? 'stock-disponible' : 'stock-agotado'; ?>">
                    <ion-icon name="cube-outline"></ion-icon>
                    <?php echo ($producto['stock'] > 0) ? 'Stock: ' . (int) $producto['stock'] : 'Agotado'; ?>
                </span>
            </div>

            <div class="producto-detalle-precio">
                <?php if (!empty($producto['precio_oferta'])): ?>
                    <span class="precio-original">$<?php echo number_format((float) $producto['precio'], 2); ?></span>
                    <span class="precio-oferta">$<?php echo number_format((float) $producto['precio_oferta'], 2); ?> En oferta</span>
                <?php else: ?>
                    <span class="precio">$<?php echo number_format((float) $producto['precio'], 2); ?></span>
                <?php endif; ?>
            </div>

            <div class="producto-detalle-descripcion">
                <h2>Descripción</h2>
                <p>
                    <?php echo !empty($producto['descripcion'])
                        ? nl2br(htmlspecialchars($producto['descripcion']))
                        : 'Este producto todavía no tiene una descripción detallada.'; ?>
                </p>
            </div>

            <div class="producto-detalle-acciones">
                <?php if (($producto['stock'] ?? 0) > 0): ?>
                    <form action="<?php echo $_ENV['BASE_URL'] . '/carrito/agregar'; ?>" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo (int) $producto['id']; ?>">
                        <button type="submit" class="btn btn-primary btn-detalle-carrito">
                            <ion-icon name="cart-outline"></ion-icon>
                            Agregar al Carrito
                        </button>
                    </form>
                <?php else: ?>
                    <div class="producto-detalle-no-stock">
                        <ion-icon name="close-circle-outline"></ion-icon>
                        Este producto está agotado por el momento.
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                    <div class="producto-detalle-admin-actions">
                        <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/productos/<?php echo (int) $producto['id']; ?>/editar" class="btn-action btn-edit">
                            <ion-icon name="pencil-outline"></ion-icon>
                            Editar producto
                        </a>
                        <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/admin/productos/<?php echo (int) $producto['id']; ?>/eliminar" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                            <button type="submit" class="btn-action btn-delete">
                                <ion-icon name="trash-outline"></ion-icon>
                                Eliminar producto
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>