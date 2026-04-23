<div class="form-container">
    <i class="fas fa-user-plus"></i>
    <h2><?php echo $title ?? 'Crear Usuario'; ?></h2>
    <p><?php echo $message ?? ''; ?></p>
    
    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="error">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/usuarios">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre"  
                   value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos"  
                   value="<?php echo htmlspecialchars($_POST['apellidos'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="Contraseña (mín. 8 caracteres)" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Confirmar Contraseña:</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirmar contraseña" required>
        </div>
        
        <div class="form-group">
            <label for="rol">Rol de Usuario:</label>
            <select id="rol" name="rol" required>
                <option value="usuario">Usuario Normal</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
        <a href="<?php echo BASE_URL; ?>/" class="btn btn-secondary">Cancelar</a>
    </form>
</div>