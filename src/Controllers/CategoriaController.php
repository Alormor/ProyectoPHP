<?php

namespace Controllers;

use Core\Controller;
use Repositories\CategoriaRepository;

class CategoriaController extends Controller
{
    private $categoriaRepository;

    public function __construct()
    {
        $this->categoriaRepository = new CategoriaRepository();
    }

    public function index()
    {
        $categorias = $this->categoriaRepository->findAll();

        $data = [
            'title' => 'Categorías',
            'message' => 'Todas las categorías disponibles',
            'categorias' => $categorias,
            'showHeader' => true,
            'showFooter' => true
        ];

        return $this->view('categorias/index', $data);
    }
    
    public function show($id)
    {
        $categoria = $this->categoriaRepository->find($id);

        if (!$categoria) {
            $this->redirect('/categorias');
            return;
        }

        $data = [
            'title' => $categoria['nombre'] ?? 'Categoría',
            'categoria' => $categoria,
            'showHeader' => true,
            'showFooter' => true
        ];

        return $this->view('categorias/show', $data);
    }
    
    public function create()
    {
        // Mostrar formulario de creación
    }
    
    public function store()
    {
        // Guardar una nueva categoría
    }
    
    public function edit($id)
    {
        // Mostrar formulario de edición
    }
    
    public function update($id)
    {
        // Actualizar una categoría
    }
    
    public function delete($id)
    {
        // Eliminar una categoría
    }
}
?>
