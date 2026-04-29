<div class="form-container">
    <ion-icon name="list-outline" class="form-icon"></ion-icon>
    <h2><?php echo $title ?? 'Formulario Categoría'; ?></h2>
    <p><?php echo $message ?? ''; ?></p>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="error">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <?php 
    $categoria = $categoria ?? null;
    ?>

    <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/admin/categorias<?php echo ($categoria) ? '/' . htmlspecialchars($categoria['id']) : ''; ?>">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre de la categoría" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['nombre'] ?? $categoria['nombre'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" placeholder="Descripción de la categoría" rows="4"><?php echo htmlspecialchars($_SESSION['form_data']['descripcion'] ?? $categoria['descripcion'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <?php echo ($categoria) ? 'Actualizar Categoría' : 'Crear Categoría'; ?>
        </button>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/categorias/gestionar" class="btn btn-secondary">Cancelar</a>
    </form>

    <?php
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']);
    }
    ?>
</div>
