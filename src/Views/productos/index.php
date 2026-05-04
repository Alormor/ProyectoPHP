<div class="productos-container">
    <h1><?php echo htmlspecialchars($title ?? ''); ?></h1>
    <p><?php echo htmlspecialchars($message ?? ''); ?></p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="error-message">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <?php $isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'; ?>

    <div class="productos-controles">
        <div class="filtro-productos">
            <input type="text" id="filtro-nombre" placeholder="Filtrar por nombre...">
            <select id="filtro-categoria">
                <option value="">Todas las categorías</option>
                <?php foreach (($categorias ?? []) as $categoria): ?>
                    <option value="<?php echo htmlspecialchars($categoria['id']); ?>">
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($isAdmin): ?>
            <div class="admin-acciones">
                <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/productos/crear" class="btn btn-primary">
                    <ion-icon name="add-circle-outline"></ion-icon> Crear Producto
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($productos)): ?>
        <p>No hay productos disponibles en este momento.</p>
    <?php else: ?>
        <div class="productos-grid" id="productos-grid">
            <?php foreach ($productos as $producto): ?>
                    <div class="producto-card" id="prod-<?= $producto['id'] ?>" data-categoria="<?php echo htmlspecialchars($producto['categoria_id']); ?>" data-nombre="<?php echo htmlspecialchars(strtolower($producto['nombre'])); ?>">
                        <form action="<?php echo $_ENV['BASE_URL'] . '/carrito/agregar'; ?>" method="POST" >
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
                                        <span class="precio-oferta">
                                            $<?php echo number_format($producto['precio_oferta'], 2); ?> En oferta
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

                            <input type="hidden" name="producto_id" value="<?= $producto["id"] ?>">
                            <div class="producto-acciones">
                                <a href="<?php echo $_ENV['BASE_URL']; ?>/productos/<?php echo $producto['id']; ?>"
                                class="btn-ver-detalles">Ver Detalles</a>
                                <?php if ($producto['stock'] > 0): ?>
                                    <button type="submit" class="btn-agregar-carrito">Agregar al Carrito</button>
                                <?php endif; ?>
                            </div>
                </form>
                        <?php if ($isAdmin): ?>
                            <div class="producto-acciones-admin">
                                <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/productos/<?php echo $producto['id']; ?>/editar" class="btn-action btn-edit" title="Editar">
                                    <ion-icon name="pencil-outline"></ion-icon> Editar
                                </a>
                                <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/admin/productos/<?php echo $producto['id']; ?>/eliminar" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                    <button type="submit" class="btn-action btn-delete" title="Eliminar" style="border: none; background: none; padding: 0; cursor: pointer;">
                                        <ion-icon name="trash-outline"></ion-icon> Eliminar
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($paginationHtml)): ?>
            <div class="productos-pagination">
                <?php echo $paginationHtml; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

