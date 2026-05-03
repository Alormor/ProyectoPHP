<?php if (isset($_SESSION['usuario'])): ?>
    <!-- Añadimos la clase "user-si" que definiste en tu CSS -->
    <div class="user-si"> 
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($_SESSION['usuario']['email']); ?></p>
        <p>Rol: <?php echo htmlspecialchars($_SESSION['usuario']['rol']); ?></p>

        <div class="profile-actions">
            <!-- Usamos las clases de botones que tienes en tu CSS -->
            <a href="<?php echo $_ENV['BASE_URL']; ?>/profile/<?php echo $_SESSION['usuario']['id']; ?>/editar" class="enlace">Editar Mi Perfil</a>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/logout" class="enlace-cerrar">Cerrar Sesión</a>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/profile/<?php echo $_SESSION['usuario']['id']; ?>/confirmar-eliminacion" class="enlace-eliminar">Eliminar Mi Cuenta</a>
        </div>
    </div>
<?php else: ?>
    <!-- Añadimos la clase "user-no" -->
    <div class="user-no">
        <h2>No has iniciado sesión</h2>
        <p>Por favor inicia sesión para acceder a tu cuenta</p>
        
        <div class="enlaces-no-user">
            <a href="<?php echo $_ENV['BASE_URL']; ?>/login" class="enlace">Iniciar Sesión</a>
            <div class="registro-row">
                <span>¿No tienes cuenta?</span>
                <a href="<?php echo $_ENV['BASE_URL']; ?>/registro" class="enlace-registro">Regístrate aquí</a>
            </div>
        </div>
    </div>
<?php endif; ?>