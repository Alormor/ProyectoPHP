<div class="recuperar-pass-container">
    <h2>Recuperar Contraseña</h2>
    <p>Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.</p>
    
    <form action="<?= $_ENV['BASE_URL'] ?>/passOlvidada" method="POST">
        <input type="email" name="email" placeholder="Tu email" required>
        <button type="submit">Enviar enlace</button>
    </form>

    <a href="<?= $_ENV['BASE_URL'] ?>/login" class="volver-link">Volver al inicio de sesión</a>
</div>