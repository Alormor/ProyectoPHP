<h2>Registrarse</h2>

<form action="<?= BASE_URL ?>/registro" method="POST">
    <div>
        <label for="email">Email:</label>
        <input type="email" name="data[email]" id="email" required>
    </div>
    <div>
        <label for="password">Contraseña:</label>
        <input type="password" name="data[password]" id="password" required>
    </div>
    <div>
        <label for="password_confirm">Confirmar contraseña:</label>
        <input type="password" name="data[password_confirm]" id="password_confirm" required>
    </div>
    <button type="submit">Registrarse</button>
</form>

<?php if (isset($_SESSION['errors'])): ?>
    <div class="error">
        <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif; ?>