<div class="delete-confirm-container">
    <div class="delete-confirm-card">
        <ion-icon name="alert-circle-outline" class="alert-icon"></ion-icon>
        <h2><?php echo $title ?? 'Confirmar Eliminación'; ?></h2>
        <p><?php echo $message ?? ''; ?></p>

        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="error-message">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <div class="usuario-info">
            <div class="info-row">
                <span class="label">Nombre:</span>
                <span class="value"><?php echo htmlspecialchars($usuario['nombre'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Apellidos:</span>
                <span class="value"><?php echo htmlspecialchars($usuario['apellidos'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value"><?php echo htmlspecialchars($usuario['email']); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Rol:</span>
                <span class="value">
                    <span class="rol-badge rol-<?php echo strtolower($usuario['rol']); ?>">
                        <?php echo ucfirst(htmlspecialchars($usuario['rol'])); ?>
                    </span>
                </span>
            </div>
        </div>

        <div class="warning-box">
            <p><strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer. <?php echo isset($es_perfil) && $es_perfil ? 'Tu cuenta será eliminada permanentemente del sistema.' : 'El usuario será eliminado permanentemente del sistema.'; ?></p>
        </div>

        <?php if (isset($es_perfil) && $es_perfil): ?>
            <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/profile/<?php echo htmlspecialchars($usuario['id']); ?>/eliminar">
                <button type="submit" class="btn btn-danger">
                    <ion-icon name="trash-outline"></ion-icon> Eliminar Mi Cuenta
                </button>
                <a href="<?php echo $_ENV['BASE_URL']; ?>/" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php else: ?>
            <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios/<?php echo htmlspecialchars($usuario['id']); ?>/eliminar">
                <button type="submit" class="btn btn-danger">
                    <ion-icon name="trash-outline"></ion-icon> Eliminar Usuario
                </button>
                <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</div>
