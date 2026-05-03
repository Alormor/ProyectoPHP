<?php

namespace Controllers;

use Core\Controller;
use Controllers\ProductoController;
use 
use Request\AdminRequest;

class PedidoController extends Controller
{
    protected $adminRequest;
    
    public function index()
    {

    }
    
    public function show($id)
    {
        // Mostrar detalles de un pedido
    }
    
    public function misPedidos()
    {
        // Mostrar pedidos del usuario autenticado
    }
    
    public function create()
    {
        // Mostrar formulario de creación
    }
    
    public function store()
    {
        // Guardar un nuevo pedido
    }
    
    public function updateEstado($id)
    {
        // Actualizar el estado de un pedido
    }
    
    public function cancelar($id)
    {
        // Cancelar un pedido
    }
}
?>
