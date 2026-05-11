<div class="recuperar-pass-container">
    <h2>Recuperar Contraseña</h2>
    <p>Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.</p>
    
    <form action="<?= $_ENV['BASE_URL'] ?>/passOlvidada" method="POST">
        <input type="email" name="email" placeholder="Tu email" required>
        <button type="submit">Enviar enlace</button>
    </form>

    <!-- Mensaje de Éxito -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success">
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Mensaje de Error -->
    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['errors'], $_SESSION['old_register']); ?>
        </div>
    <?php endif; ?>

    <a href="<?= $_ENV['BASE_URL'] ?>/login" class="volver-link">Volver al inicio de sesión</a>
</div>