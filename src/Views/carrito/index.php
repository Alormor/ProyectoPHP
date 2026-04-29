<div class="carrito-container">
    <h1><?= $title ?></h1>

    <?php if (empty($items)): ?>
        <p>Tu carrito está vacío.</p>
        <a href="<?= $_ENV['BASE_URL'] ?>/productos" class="btn">Ir a la tienda</a>
    <?php else: ?>
        <table class="carrito-tabla">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($items as $item): ?>
                    <?php $subtotal = $item['precio'] * $item['cantidad']; $total += $subtotal; ?>
                    <tr>
                        <td><img src="<?= $item['imagen'] ?>" width="50"></td>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td><?= number_format($item['precio'], 2) ?>€</td>
                        <td>
                            <div class="cantidad-controles">
                                <a href="<?= $_ENV['BASE_URL'] ?>/carrito/decrementar/<?= $item['producto_id'] ?>" class="btn-cantidad">-</a>
                                <span class="cantidad-numero"><?= $item['cantidad'] ?></span>
                                <a href="<?= $_ENV['BASE_URL'] ?>/carrito/incrementar/<?= $item['producto_id'] ?>" class="btn-cantidad">+</a>
                            </div>
                        </td>
                        <td><?= number_format($subtotal, 2) ?>€</td>
                        <td>
                            <a href="<?= $_ENV['BASE_URL'] ?>/carrito/eliminar/<?= $item['producto_id'] ?>" class="btn-danger">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="carrito-resumen">
            <h3>Total: <?= number_format($total, 2) ?>€</h3>
            <a href="<?= $_ENV['BASE_URL'] ?>/carrito/vaciar" class="btn-warning">Vaciar Carrito</a>
            <a href="<?= $_ENV['BASE_URL'] ?>/pedidos/checkout" class="btn-success">Finalizar Pedido</a>
        </div>
    <?php endif; ?>
</div>
