<div class="home-container">
    <h1><?php echo $title ?? 'Página'; ?></h1>
    <p><?php echo $message ?? ''; ?></p>
    
    <?php if (isset($_SESSION['usuario'])): ?>
        <!-- Vista cuando el usuario está autenticado -->
        <div class="user-welcome">
            <div class="welcome-card">
                <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>!</h2>
                <p class="user-email">Email: <?php echo htmlspecialchars($_SESSION['usuario']['email']); ?></p>
                <p class="user-role">
                    Rol: 
                    <strong>
                        <?php 
                            $rol = $_SESSION['usuario']['rol'];
                            echo $rol === 'admin' ? 'Administrador' : 'Usuario Normal';
                        ?>
                    </strong>
                </p>
                
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($_SESSION['success']); ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
            </div>
            
            <div class="home-buttons">
                <a href="<?php echo $_ENV['BASE_URL']; ?>/productos" class="btn-productos">Ver Productos</a>
                <a href="<?php echo $_ENV['BASE_URL']; ?>/logout" class="btn-logout">Cerrar Sesión</a>

                <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios" class="btn-admin">Gestión de Usuarios</a>
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/usuarios/crear" class="btn-admin">Crear Usuario</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Vista cuando no hay usuario autenticado -->
        <div class="home-buttons">
            <a href="<?php echo $_ENV['BASE_URL']; ?>/productos" class="btn-productos">Productos</a>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/registro" class="btn-registro">Registrarse</a>
            <a href="<?php echo $_ENV['BASE_URL']; ?>/login" class="btn-login">Iniciar Sesión</a>
        </div>
    <?php endif; ?>
</div>
