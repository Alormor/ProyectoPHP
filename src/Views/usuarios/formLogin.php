<div>
    <i id="inicio-sesion" class="fas fa-user-circle"></i>
    <h2>Iniciar Sesión</h2>
    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="error">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success">
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?> 
        
    <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/login">
        <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Acceder</button>
    </form>
    <p>¿Has olvidado tu contraseña? <a href="<?php echo $_ENV['BASE_URL']; ?>/passOlvidada">Recuperar contraseña</a></p>

    <p>¿No tienes cuenta? <a href="<?php echo $_ENV['BASE_URL']; ?>/registro">Regístrate aquí</a></p>
</div>