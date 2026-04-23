<?php if (isset($_SESSION['usuario'])): ?>
    <div>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($_SESSION['usuario']['email']); ?></p>
        <p>Rol: <?php echo htmlspecialchars($_SESSION['usuario']['rol']); ?></p>
        <a href="<?php echo BASE_URL; ?>/logout">Cerrar Sesión</a>
    </div>
<?php else: ?>
    <div>
        <h2>No has iniciado sesión</h2>
        <p>Por favor inicia sesión para acceder a tu cuenta</p>
        <a href="<?php echo BASE_URL; ?>/login">Iniciar Sesión</a>
        <p>¿No tienes cuenta? <a href="<?php echo BASE_URL; ?>/registro">Regístrate aquí</a></p>
    </div>
<?php endif; ?>
