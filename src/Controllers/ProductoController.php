<?php

namespace Controllers;

use Core\Controller;

class ProductoController extends Controller
{
    public function index()
    {
        $data=[
            'title' => 'Listado de Productos',
            'message' => 'Mostrando todos los productos disponibles',
            'showHeader' => true,
            'showFooter' => true
        ];
        
        return $this->view('productos/index', $data);
    }
    
    public function show($id)
    {
        // Mostrar detalles de un producto
    }
    
    public function create()
    {
        // Mostrar formulario de creación
    }
    
    public function store()
    {
        // Guardar un nuevo producto
    }
    
    public function edit($id)
    {
        // Mostrar formulario de edición
    }
    
    public function update($id)
    {
        // Actualizar un producto
    }
    
    public function delete($id)
    {
        // Eliminar un producto
    }
    
    public function buscar()
    {
        // Buscar productos por término
    }


}
