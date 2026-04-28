<?php if (isset($_SESSION['usuario'])): ?>
    <div>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($_SESSION['usuario']['email']); ?></p>
        <p>Rol: <?php echo htmlspecialchars($_SESSION['usuario']['rol']); ?></p>

        <div class="profile-actions">
            <a href="<?php echo $_ENV['BASE_URL']; ?>/profile/<?php echo $_SESSION['usuario']['id']; ?>/editar" class="btn btn-primary">Editar Mi Perfil</a>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/logout" class="btn btn-secondary">Cerrar Sesión</a>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/profile/<?php echo $_SESSION['usuario']['id']; ?>/confirmar-eliminacion" class="btn btn-danger">Eliminar Mi Cuenta</a>
        </div>
    </div>
<?php else: ?>
    <div>
        <h2>No has iniciado sesión</h2>
        <p>Por favor inicia sesión para acceder a tu cuenta</p>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/login">Iniciar Sesión</a>
        <p>¿No tienes cuenta? <a href="<?php echo $_ENV['BASE_URL']; ?>/registro">Regístrate aquí</a></p>
    </div>
<?php endif; ?>
