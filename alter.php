<?php
require_once 'vendor/autoload.php';

use Core\BaseDatos;

try {
    $db = BaseDatos::getInstancia();
    $sql = "ALTER TABLE usuarios ADD COLUMN direccion VARCHAR(255);";
    $result = $db->ejecutar($sql);
    if ($result) {
        echo "Columna 'direccion' agregada exitosamente.\n";
    } else {
        echo "Error al agregar la columna.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>