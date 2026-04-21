<?php include __DIR__ . '/../shared/header.php'; ?>

<div class="error-container" style="text-align: center; padding: 60px 20px;">
    <div class="error-code" style="font-size: 120px; font-weight: bold; color: #667eea; margin-bottom: 20px;"><?php echo $code; ?></div>
    <h1 style="font-size: 32px; color: #333; margin-bottom: 10px;"><?php echo $message; ?></h1>
    <p style="color: #666; font-size: 16px; margin-bottom: 30px;">Lo sentimos, algo salió mal.</p>
    <a href="<?php echo BASE_URL; ?>/" style="display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Volver al inicio</a>
</div>

<?php include __DIR__ . '/../shared/footer.php'; ?>
