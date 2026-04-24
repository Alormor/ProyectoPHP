<div class="usuarios-container">
    <h1><?php echo $title ?? 'Usuarios'; ?></h1>
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
        <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios/crear" class="btn btn-primary">
            <ion-icon name="add-circle-outline"></ion-icon> Crear Nuevo Usuario
        </a>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/" class="btn btn-secondary">
            <ion-icon name="arrow-back-outline"></ion-icon> Volver al Inicio
        </a>
    </div>

    <?php if (!empty($usuarios)): ?>
        <div class="table-responsive">
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Confirmado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellidos'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
                                <span class="rol-badge rol-<?php echo strtolower($usuario['rol']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="confirmado-badge confirmado-<?php echo $usuario['confirmado'] ? 'si' : 'no'; ?>">
                                    <?php echo $usuario['confirmado'] ? 'Sí' : 'No'; ?>
                                </span>
                            </td>
                            <td class="acciones-cell">
                                <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios/<?php echo $usuario['id']; ?>/editar" class="btn-action btn-edit" title="Editar">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </a>
                                <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios/<?php echo $usuario['id']; ?>/confirmar-eliminacion" class="btn-action btn-delete" title="Eliminar">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>No hay usuarios registrados aún.</p>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios/crear" class="btn btn-primary">Crear el primer usuario</a>
        </div>
    <?php endif; ?>
</div>
