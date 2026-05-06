<div class="confirmar-envio-container">
    <h2>Confirmar Datos de Envío</h2>

    <p>Para finalizar tu pedido, necesitamos confirmar los datos de envío.</p>

    <form action="<?= $_ENV['BASE_URL'] ?>/pedidos/guardar-direccion" method="POST">
        <div>
            <label for="provincia">Provincia:</label>
            <input type="text" name="provincia" id="provincia" required>
        </div>
        <div>
            <label for="localidad">Localidad:</label>
            <input type="text" name="localidad" id="localidad" required>
        </div>
        <div>
            <label for="direccion">Dirección:</label>
            <input type="text" name="direccion" id="direccion" required>
        </div>
        <button type="submit">Confirmar y proceder al pago</button>
    </form>

    <!-- Mensaje de Error -->
    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>

    <p><a href="<?php echo $_ENV['BASE_URL']; ?>/carrito">Volver al carrito</a></p>
</div>