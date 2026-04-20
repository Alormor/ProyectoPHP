<?php

namespace Models;

class Model
{
    protected $db;
    protected $table;
    
    public function __construct()
    {
        // Inicializar conexión a base de datos
    }
    
    public function all()
    {
        // Obtener todos los registros
    }
    
    public function find($id)
    {
        // Obtener un registro por ID
    }
    
    public function create($data)
    {
        // Crear un nuevo registro
    }
    
    public function update($id, $data)
    {
        // Actualizar un registro
    }
    
    public function delete($id)
    {
        // Eliminar un registro
    }
}
?>
