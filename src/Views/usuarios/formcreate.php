<div>
    <i id="inicio-sesion" class="fas fa-user-circle"></i>
    <h2>Iniciar Sesión</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" action="index.php">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" name="login">Acceder</button>
        <p>Usuario Laura y contraseña Laura2005</p>
    </form>
</div>