<div class="home-container">
    <?php if (isset($_SESSION['usuario'])): ?>
        <div class="user-welcome">
            <div class="welcome-card">
                <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>!</h2>
                <p class="user-email">Email: <?php echo htmlspecialchars($_SESSION['usuario']['email']); ?></p>
                <p class="user-role">
                    Rol: <strong><?php echo $_SESSION['usuario']['rol'] === 'admin' ? 'Administrador' : 'Usuario Normal'; ?></strong>
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
        <div class="contenedor-noInicio">
            
            <div class="hero-content">
                <span class="tagline">DESAFÍA TU MENTE</span>
                
                <h1>Descubre tu <span>próximo desafío</span></h1>
                
                <p class="hero-description">
                    Los mejores cubos de Rubik para todos los niveles.<br>
                    Calidad, velocidad y diversión.
                </p>
                
                <div class="home-buttons">
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/productos" class="btn-productos">
                        <ion-icon name="cube-outline"></ion-icon> Ver Productos
                    </a>
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/registro" class="btn-registro">
                        Registrarse
                    </a>
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/login" class="btn-login">
                        Iniciar Sesión
                    </a>
                </div>

                <div class="caracteristicas">
                    <div class="item-caracteristica">
                        <ion-icon name="airplane-outline"></ion-icon>
                        <div class="texto-caracteristica">
                            <strong>Envíos rápidos</strong>
                            <span>a toda España</span>
                        </div>
                    </div>
                    
                    <div class="item-caracteristica">
                        <ion-icon name="ribbon-outline"></ion-icon>
                        <div class="texto-caracteristica">
                            <strong>Productos</strong>
                            <span>100% originales</span>
                        </div>
                    </div>
                    
                    <div class="item-caracteristica">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        <div class="texto-caracteristica">
                            <strong>Pago seguro</strong>
                            <span>y protegido</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-image">
                <img class="img-inicio" src="<?php echo $_ENV['BASE_URL']; ?>/images/inicio.png" alt="Foto cubo rubik inicio">
            </div>

        </div>
    <?php endif; ?>
</div>