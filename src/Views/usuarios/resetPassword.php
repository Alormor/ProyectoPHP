<h2>Recuperar Contraseña</h2>
<form action="<?= $_ENV['BASE_URL'] ?>/resetPassword" method="POST">
    <!-- El token es invisible pero necesario -->
    <input type="hidden" name="token" value="<?= $token ?>">
    
    <label>Nueva contraseña:</label>
    <input type="password" name="password" required>
    
    <label>Confirmar contraseña:</label>
    <input type="password" name="confirm_password" required>
    
    <button type="submit">Cambiar contraseña</button>
</form>

<!-- Mensaje de Éxito -->
<?php if (isset($_SESSION['register']) && $_SESSION['register'] == 'success'): ?>
    <div class="alert alert-success">
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['register'], $_SESSION['message']); ?>
<?php endif; ?>

<!-- Mensaje de Error -->
<?php if (isset($_SESSION['errors'])): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>