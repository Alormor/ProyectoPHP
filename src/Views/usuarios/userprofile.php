<?php if (isset($_SESSION['usuario'])): ?>
    <div class="user-si">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($_SESSION['usuario']['email']); ?></p>
        <p>Rol: <?php echo htmlspecialchars($_SESSION['usuario']['rol']); ?></p>
        <div class="enlaces-si-user">
            <a class="enlace" href="<?php echo $_ENV['BASE_URL']; ?>/logout">Cerrar Sesión</a>
        </div>
    </div>
<?php else: ?>
    <div class="user-no">
        <h2>No has iniciado sesión</h2>
        <p>Por favor inicia sesión para acceder a tu cuenta</p>
        <div class="enlaces-no-user">
            <a class="enlace" href="<?php echo $_ENV['BASE_URL']; ?>/login">Iniciar Sesión</a>
            <div class="registro-row">
                <span>¿No tienes cuenta?</span> 
                <a class="enlace-registro" href="<?php echo $_ENV['BASE_URL']; ?>/registro">Regístrate aquí</a>
            </div>
        </div>
    </div>
<?php endif; ?>