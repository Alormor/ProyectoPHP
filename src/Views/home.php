<div class="home-container">
    <h1><?php echo $title ?? 'Página'; ?></h1>
    <p><?php echo $message ?? ''; ?></p>
    
    <div class="home-buttons">
        <a href="<?php echo BASE_URL; ?>/productos" class="btn-productos">Productos</a>
        <?php if (!isset($_SESSION['usuario'])): ?>
            <a href="<?php echo BASE_URL; ?>/registro" class="btn-registro">Registrarse</a>
            <a href="<?php echo BASE_URL; ?>/login" class="btn-login">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</div>
