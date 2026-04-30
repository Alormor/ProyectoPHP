<div class="form-container">
    <ion-icon name="person-outline" class="form-icon"></ion-icon>
    <h2><?php echo $title ?? 'Editar Usuario'; ?></h2>
    <p><?php echo $message ?? ''; ?></p>

    <?php
    $isAdmin = !empty($es_admin);
    $usuarioData = $usuario ?? [];
    ?>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="error">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success">
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php
    // Determinar el contexto (perfil propio o edición de admin)
    $es_perfil = isset($es_perfil) && $es_perfil;
    $form_action = $es_perfil 
        ? $_ENV['BASE_URL'] . '/profile/' . htmlspecialchars($usuario['id']) 
        : $_ENV['BASE_URL'] . '/admin/usuarios/' . htmlspecialchars($usuario['id']);
    $cancelar_url = $es_perfil 
        ? $_ENV['BASE_URL'] . '/usuarios/userprofile' 
        : $_ENV['BASE_URL'] . '/admin/usuarios';
    ?>

    <form method="POST" action="<?php echo $form_action; ?>">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['nombre'] ?? $usuarioData['nombre'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['apellidos'] ?? $usuarioData['apellidos'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? $usuarioData['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="rol">Rol de Usuario:</label>
            <select id="rol" name="rol" required>
                <option value="usuario" <?php echo (($_SESSION['form_data']['rol'] ?? $usuario['rol'] ?? 'usuario') === 'usuario') ? 'selected' : ''; ?>>Usuario Normal</option>
                <option value="admin" <?php echo (($_SESSION['form_data']['rol'] ?? $usuario['rol'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios" class="btn btn-secondary">Cancelar</a>
    </form>

    <?php
    // Limpiar datos del formulario después de mostrar errores/éxito
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']);
    }
    ?>
</div>
