<h2 class="registro">Registrarse</h2>

<form class="form-registro" action="<?= $_ENV['BASE_URL'] ?>/registro" method="POST">
    <div class="nombre">
        <label for="nombre">Nombre:</label>
        <input type="text" name="data[nombre]" id="nombre" required>
    </div>
    <div class="apellidos">
        <label for="apellidos">Apellidos:</label>
        <input type="text" name="data[apellidos]" id="apellidos" required>
    </div>
    <div class="email">
        <label for="email">Email:</label>
        <input type="email" name="data[email]" id="email" required>
    </div>
    <div class="direccion">
        <label for="direccion">Dirección:</label>
        <input type="text" name="data[direccion]" id="direccion" required>
    </div>
    <div class="passw">
        <label for="password">Contraseña:</label>
        <input type="password" name="data[password]" id="password" required>
    </div>
    <div class="passw-confirm">
        <label for="password_confirm">Confirmar contraseña:</label>
        <input type="password" name="data[password_confirm]" id="password_confirm" required>
    </div>
    <button type="submit">Registrarse</button>
    <p><a class="volver" href="<?php echo $_ENV['BASE_URL']; ?>/">Inicio</a></p>
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