
//<!-- Vistas para pedidos -->
<div class="mis-pedidos">
    <h2>Mis Pedidos</h2>

    <?php if (empty($pedidos)): ?>
        <p>No has realizado ningún pedido todavía.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Provincia</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= $pedido['id'] ?></td>
                        <td><?= $pedido['fecha_pedido'] ?? '-' ?></td>
                        <td><?= $pedido['provincia'] ?></td>
                        <td><?= number_format($pedido['coste_total'], 2) ?> €</td>
                        <td>
                            <span class="estado <?= $pedido['estado'] ?>">
                                <?= ucfirst($pedido['estado']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>