<div class="usuarios-container">
    <h1><?php echo $title ?? 'Categorías'; ?></h1>
    <p><?php echo $message ?? ''; ?></p>

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

    <div class="usuarios-acciones">
        <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/categorias/crear" class="btn btn-primary">
            <ion-icon name="add-circle-outline"></ion-icon> Crear Nueva Categoría
        </a>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/" class="btn btn-secondary">
            <ion-icon name="arrow-back-outline"></ion-icon> Volver al Inicio
        </a>
    </div>

    <?php if (!empty($categorias)): ?>
        <div class="table-responsive">
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($categoria['id']); ?></td>
                            <td><?php echo htmlspecialchars($categoria['nombre'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(substr($categoria['descripcion'] ?? 'Sin descripción', 0, 60)); ?></td>
                            <td class="acciones-cell">
                                <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/categorias/<?php echo $categoria['id']; ?>/editar" class="btn-action btn-edit" title="Editar">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </a>
                                <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/admin/categorias/<?php echo $categoria['id']; ?>/eliminar" style="display: inline;" onsubmit="return confirm('¿Estás seguro?');">
                                    <button type="submit" class="btn-action btn-delete" title="Eliminar" style="border: none; background: none; padding: 0; cursor: pointer;">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>No hay categorías registradas aún.</p>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/categorias/crear" class="btn btn-primary">Crear la primera categoría</a>
        </div>
    <?php endif; ?>
</div>
