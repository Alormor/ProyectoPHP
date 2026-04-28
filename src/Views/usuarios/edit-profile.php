<div class="form-container">
    <ion-icon name="person-outline" class="form-icon"></ion-icon>
    <h2><?php echo $title ?? 'Editar Mi Perfil'; ?></h2>
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

    <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/profile/<?php echo htmlspecialchars($usuario['id']); ?>">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['nombre'] ?? $usuario['nombre'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['apellidos'] ?? $usuario['apellidos'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? $usuario['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="rol">Rol de Usuario:</label>
            <input type="text" id="rol" name="rol" disabled value="<?php echo htmlspecialchars($usuario['rol']); ?>">
            <small>No puedes cambiar tu rol</small>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/" class="btn btn-secondary">Cancelar</a>
    </form>

    <?php
    // Limpiar datos del formulario después de mostrar errores/éxito
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']);
    }
    ?>
</div>
