<div class="login">
    <div class="container">
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
            <p>Email</p>
            <input class="input-email" type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <p>Password</p>
            <input class="input-passw" type="password" name="password" placeholder="Contraseña" required>
            <button class="btn-acceso" type="submit">Acceder</button>
        </form>
        
        <p>¿No tienes cuenta? <a href="<?php echo $_ENV['BASE_URL']; ?>/registro">Regístrate aquí</a></p>
        <p><a class="volver-inicio" href="<?php echo $_ENV['BASE_URL']; ?>/">Inicio</a></p>
    </div>
</div>