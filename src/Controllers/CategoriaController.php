<?php

namespace Controllers;

use Repositories\CategoriaRepository;

class CategoriaController extends AdminController
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
    
    public function gestion()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        try {
            $categorias = $this->categoriaRepository->findAll();

            $data = $this->prepararDatosVista(
                'Gestion de Categorias',
                'Administra todas las categorías de la tienda.',
                ['categorias' => $categorias]
            );
            return $this->view('categorias/gestion', $data);
        } catch (\Exception $e) {
            $this->guardarError('Error al cargar las categorías: ' . $e->getMessage());
            $this->redirect('/');
            return;
        }
    }
    
    public function create()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $data = $this->prepararDatosVista(
            'Crear Categoría',
            'Rellena los datos para crear una nueva categoría.',
            [
                'categoria' => null,
                'modo' => 'crear'
            ]
        );

        return $this->view('categorias/form', $data);
    }
    
    public function store()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $errors = [];
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }

        $formData = ['nombre' => $nombre, 'descripcion' => $descripcion];
        $this->guardarErroresYRedirigir($errors, $formData, '/admin/categorias/crear');
        if (!empty($errors)) {
            return;
        }

        $resultado = $this->categoriaRepository->create([
            'nombre' => $nombre,
            'descripcion' => $descripcion ?: null
        ]);

        if ($resultado) {
            $this->guardarExito('Categoría creada correctamente.');
            $this->redirect('/admin/categorias/gestionar');
            return;
        }

        $this->guardarErroresYRedirigir(['No se pudo crear la categoría.'], $formData, '/admin/categorias/crear');
    }
    
    public function edit($id)
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $categoria = $this->categoriaRepository->find((int) $id);

        if (!$categoria) {
            $this->guardarError('La categoría no existe.');
            $this->redirect('/admin/categorias/gestionar');
            return;
        }

        $data = $this->prepararDatosVista(
            'Editar Categoría',
            'Actualiza los datos de la categoría.',
            [
                'categoria' => $categoria,
                'modo' => 'editar'
            ]
        );

        return $this->view('categorias/form', $data);
    }
    
    public function update($id)
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $categoria = $this->categoriaRepository->find((int) $id);
        if (!$categoria) {
            $this->guardarError('La categoría no existe.');
            $this->redirect('/admin/categorias/gestionar');
            return;
        }

        $errors = [];
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }

        $formData = ['nombre' => $nombre, 'descripcion' => $descripcion];
        $this->guardarErroresYRedirigir($errors, $formData, '/admin/categorias/' . (int) $id . '/editar');
        if (!empty($errors)) {
            return;
        }

        $resultado = $this->categoriaRepository->update((int) $id, [
            'nombre' => $nombre,
            'descripcion' => $descripcion ?: null
        ]);

        if ($resultado) {
            $this->guardarExito('Categoría actualizada correctamente.');
            $this->redirect('/admin/categorias/gestionar');
            return;
        }

        $this->guardarErroresYRedirigir(['No se pudo actualizar la categoría.'], $formData, '/admin/categorias/' . (int) $id . '/editar');
    }
    
    public function delete($id)
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $resultado = $this->categoriaRepository->delete((int) $id);

        if ($resultado) {
            $this->guardarExito('Categoría eliminada correctamente.');
        } else {
            $this->guardarError('No se pudo eliminar la categoría.');
        }

        $this->redirect('/admin/categorias/gestionar');
    }
}
?>
