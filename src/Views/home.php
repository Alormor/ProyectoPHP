<?php include __DIR__ . '/shared/header.php'; ?>

<div style="text-align: center; margin: 40px 0;">
    <h1><?php echo $title ?? 'Página'; ?></h1>
    <p><?php echo $message ?? ''; ?></p>
    
    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin: 30px 0;">
        <a href="<?php echo BASE_URL; ?>/productos" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Productos</a>
        <?php if (!isset($_SESSION['usuario'])): ?>
            <a href="<?php echo BASE_URL; ?>/registro" style="padding: 12px 24px; background: #764ba2; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Registrarse</a>
            <a href="<?php echo BASE_URL; ?>/login" style="padding: 12px 24px; background: #50c878; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/shared/footer.php'; ?>
