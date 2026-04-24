<?php

namespace Controllers;

use Core\Controller;
use Repositories\ProductoRepository;

class ProductoController extends Controller
{
    private $productoRepository;

    public function __construct()
    {
        $this->productoRepository = new ProductoRepository();
    }

    public function index()
    {
        $productos = $this->productoRepository->findAll();

        $data = [
            'title' => 'Listado de Productos',
            'message' => 'Mostrando todos los productos disponibles',
            'productos' => $productos,
            'showHeader' => true,
            'showFooter' => true
        ];

        return $this->view('productos/index', $data);
    }

    public function show($id)
    {
        $producto = $this->productoRepository->find($id);

        if (!$producto) {
            $this->redirect('/productos');
            return;
        }

        $data = [
            'title' => $producto['nombre'] ?? 'Producto',
            'producto' => $producto,
            'showHeader' => true,
            'showFooter' => true
        ];

        return $this->view('productos/show', $data);
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

