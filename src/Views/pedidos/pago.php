<div class="pago-container">
    <h2>Resumen de tu Pedido</h2>
    
    <div class="pago-info">
        <p><strong>Dirección:</strong> <?= $direccion['direccion'] ?>, <?= $direccion['localidad'] ?></p>
        <p><strong>Precio sin descuento:</strong><span><?= number_format(($totalSin), 2) ?>€</span></p>
        <p><strong>Total a pagar:</strong> <span><?= number_format($total, 2) ?>€</span></p>
    </div>

    <div id="paypal-button-container"></div>
    <?php if (isset($_SESSION['usuario']['rol']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
    <div style="margin-top:16px;">
        <button id="simular-pago" class="btn-simular-pago">Simular pago (prueba)</button>
    </div>
    <?php endif; ?>
    
    <div class="pago-footer">
        <p>Pago 100% seguro procesado por PayPal</p>
        <p><a href="<?= $_ENV['BASE_URL'] ?>/carrito">Volver al carrito</a></p>
    </div>
</div>
