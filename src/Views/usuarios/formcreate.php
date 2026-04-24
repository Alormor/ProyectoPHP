<div class="form-container">
    <i class="fas fa-user-plus"></i>
    <h2><?php echo $title ?? 'Crear Usuario'; ?></h2>
    <p><?php echo $message ?? ''; ?></p>
    
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

    <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['nombre'] ?? $_POST['nombre'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['apellidos'] ?? $_POST['apellidos'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? $_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Contraseña (mín. 8 caracteres)" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmar Contraseña:</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirmar contraseña" required>
        </div>

        <div class="form-group">
            <label for="rol">Rol de Usuario:</label>
            <select id="rol" name="rol" required>
                <option value="usuario" <?php echo (($_SESSION['form_data']['rol'] ?? $_POST['rol'] ?? 'usuario') === 'usuario') ? 'selected' : ''; ?>>Usuario Normal</option>
                <option value="admin" <?php echo (($_SESSION['form_data']['rol'] ?? $_POST['rol'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear Usuario</button>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/" class="btn btn-secondary">Cancelar</a>
    </form>

    <?php
    // Limpiar datos del formulario después de mostrar errores/éxito
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']);
    }
    ?>
</div>