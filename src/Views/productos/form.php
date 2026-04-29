<div class="form-container">
    <ion-icon name="cube-outline" class="form-icon"></ion-icon>
    <h2><?php echo $title ?? 'Formulario Producto'; ?></h2>
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
    $categorias = $categorias ?? [];
    $producto = $producto ?? null;
    ?>

    <form method="POST" action="<?php echo $_ENV['BASE_URL']; ?>/admin/productos<?php echo ($producto) ? '/' . htmlspecialchars($producto['id']) : ''; ?>">
        <div class="form-group">
            <label for="categoria_id">Categoría:</label>
            <select id="categoria_id" name="categoria_id" required>
                <option value="">Selecciona una categoría</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id']; ?>" 
                        <?php echo (($_SESSION['form_data']['categoria_id'] ?? $producto['categoria_id'] ?? '') === (string) $categoria['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre del producto" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['nombre'] ?? $producto['nombre'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" placeholder="Descripción del producto" rows="4"><?php echo htmlspecialchars($_SESSION['form_data']['descripcion'] ?? $producto['descripcion'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="Precio" step="0.01" min="0" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['precio'] ?? $producto['precio'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="precio_oferta">Precio de Oferta:</label>
            <input type="number" id="precio_oferta" name="precio_oferta" placeholder="Precio de oferta (opcional)" step="0.01" min="0"
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['precio_oferta'] ?? $producto['precio_oferta'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" placeholder="Stock" min="0" required
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['stock'] ?? $producto['stock'] ?? '0'); ?>">
        </div>

        <div class="form-group">
            <label for="imagen">URL de Imagen:</label>
            <input type="url" id="imagen" name="imagen" placeholder="https://ejemplo.com/imagen.jpg"
                   value="<?php echo htmlspecialchars($_SESSION['form_data']['imagen'] ?? $producto['imagen'] ?? ''); ?>">
        </div>

        <button type="submit" class="btn btn-primary">
            <?php echo ($producto) ? 'Actualizar Producto' : 'Crear Producto'; ?>
        </button>
        <a href="<?php echo $_ENV['BASE_URL']; ?>/admin/productos/gestionar" class="btn btn-secondary">Cancelar</a>
    </form>

    <?php
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']);
    }
    ?>
</div>
