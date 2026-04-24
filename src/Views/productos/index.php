<div class="productos-container">
    <h1><?php echo $title; ?></h1>
    <p><?php echo $message; ?></p>

    <?php if (empty($productos)): ?>
        <p>No hay productos disponibles en este momento.</p>
    <?php else: ?>
        <div class="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
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