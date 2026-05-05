<?php

namespace Controllers;

use Core\Controller;
use Request\AdminRequest;
use Repositories\CategoriaRepository;

class CategoriaController extends Controller
{
    protected $categoriaRepository;
    protected $adminRequest;

    public function __construct()
    {
        $this->categoriaRepository = new CategoriaRepository();
        $this->adminRequest = new AdminRequest();
    }

    
    public function gestion()
    {
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        try {
            $categorias = $this->categoriaRepository->findAll();

            $data = $this->adminRequest->prepararDatosVista(
                'Gestion de Categorias',
                'Administra todas las categorías de la tienda.',
                ['categorias' => $categorias]
            );
            return $this->view('categorias/gestion', $data);
        } catch (\Exception $e) {
            $this->adminRequest->guardarError('Error al cargar las categorías: ' . $e->getMessage());
            $this->redirect('/');
            return;
        }
    }
    
    public function create()
    {
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        $data = $this->adminRequest->prepararDatosVista(
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
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        $errors = [];
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }

        $formData = ['nombre' => $nombre, 'descripcion' => $descripcion];
        $redirect = $this->adminRequest->guardarErroresYRedirigir($errors, $formData, '/admin/categorias/crear');
        if ($redirect) {
            $this->redirect($redirect);
            return;
        }

        $resultado = $this->categoriaRepository->create([
            'nombre' => $nombre,
            'descripcion' => $descripcion ?: null
        ]);

        if ($resultado) {
            $this->adminRequest->guardarExito('Categoría creada correctamente.');
            $this->redirect('/admin/categorias/gestionar');
            return;
        }

        $redirect = $this->adminRequest->guardarErroresYRedirigir(['No se pudo crear la categoría.'], $formData, '/admin/categorias/crear');
        if ($redirect) {
            $this->redirect($redirect);
            return;
        }
    }
    
    public function edit($id)
    {
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        $categoria = $this->categoriaRepository->find((int) $id);

        if (!$categoria) {
            $this->adminRequest->guardarError('La categoría no existe.');
            $this->redirect('/admin/categorias/gestionar');
            return;
        }

        $data = $this->adminRequest->prepararDatosVista(
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
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        $categoria = $this->categoriaRepository->find((int) $id);
        if (!$categoria) {
            $this->adminRequest->guardarError('La categoría no existe.');
            $this->redirect('/categorias');
            return;
        }

        $errors = [];
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }

        $formData = ['nombre' => $nombre, 'descripcion' => $descripcion];
        $redirect = $this->adminRequest->guardarErroresYRedirigir($errors, $formData, '/admin/categorias/' . (int) $id . '/editar');
        if ($redirect) {
            $this->redirect($redirect);
            return;
        }

        $resultado = $this->categoriaRepository->update((int) $id, [
            'nombre' => $nombre,
            'descripcion' => $descripcion ?: null
        ]);

        if ($resultado) {
            $this->adminRequest->guardarExito('Categoría actualizada correctamente.');
            $this->redirect('/admin/categorias/gestionar');
            return;
        }

        $redirect = $this->adminRequest->guardarErroresYRedirigir(['No se pudo actualizar la categoría.'], $formData, '/admin/categorias/' . (int) $id . '/editar');
        if ($redirect) {
            $this->redirect($redirect);
            return;
        }
    }
    
    public function delete($id)
    {
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        $resultado = $this->categoriaRepository->delete((int) $id);

        if ($resultado) {
            $this->adminRequest->guardarExito('Categoría eliminada correctamente.');
        } else {
            $this->adminRequest->guardarError('No se pudo eliminar la categoría.');
        }

        $this->redirect('/admin/categorias/gestionar');
    }
}
?>
