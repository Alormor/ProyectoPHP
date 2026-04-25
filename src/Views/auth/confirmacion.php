<div class="confirmacion-container">
    <?php if ($status === 'success'): ?>
        <div class="card success">
            <h1>¡Enhorabuena!</h1>
            <p>Tu cuenta ha sido confirmada con éxito.</p>
            <a href="<?= BASE_URL ?>/login" class="btn-login">Ir al Login</a>
        </div>

    <?php elseif ($status === 'expired'): ?>
        <div class="card warning">
            <h1>Enlace expirado</h1>
            <p>Lo sentimos, el enlace de confirmación ha caducado por seguridad.</p>
            <p>Puedes volver a registrarte con el mismo correo para recibir un nuevo enlace.</p>
            <a href="<?= BASE_URL ?>/registro" class="btn-registro">Volver a registrarme</a>
        </div>

    <?php else: ?>
        <div class="card error">
            <h1>Error de confirmación</h1>
            <p>El enlace no es válido, ha sido manipulado o ya ha sido utilizado.</p>
            <a href="<?= BASE_URL ?>/" class="btn-inicio">Volver al inicio</a>
        </div>
    <?php endif; ?>
</div>
