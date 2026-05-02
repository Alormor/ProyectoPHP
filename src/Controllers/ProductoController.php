<?php

namespace Controllers;

use Core\Controller;
use Request\AdminRequest;
use Repositories\ProductoRepository;
use Repositories\CategoriaRepository;

class ProductoController extends Controller
{
    protected $productoRepository;
    protected $categoriaRepository;
    protected $adminRequest;

    public function __construct()
    {
        $this->productoRepository = new ProductoRepository();
        $this->categoriaRepository = new CategoriaRepository();
        $this->adminRequest = new AdminRequest();
    }

    public function index()
    {
        $productos = $this->productoRepository->findAll();
        $categorias = $this->categoriaRepository->findAll();

        $data = [
            'title' => 'Listado de Productos',
            'message' => 'Mostrando todos los productos disponibles',
            'productos' => $productos,
            'categorias' => $categorias,
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
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para crear productos.'];
            $this->redirect('/');
            return;
        }

        $data = [
            'title' => 'Crear Producto',
            'message' => 'Rellena los datos para crear un nuevo producto.',
            'producto' => null,
            'categorias' => $this->categoriaRepository->findAll(),
            'modo' => 'crear',
            'showHeader' => true,
            'showFooter' => true
        ];

        return $this->view('productos/form', $data);
    }

    public function store()
    {
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        $errors = [];
        $categoria_id = (int) ($_POST['categoria_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = (float) ($_POST['precio'] ?? 0);
        $precio_oferta = trim($_POST['precio_oferta'] ?? '') === '' ? null : (float) $_POST['precio_oferta'];
        $stock = (int) ($_POST['stock'] ?? 0);
        $imagen = trim($_POST['imagen'] ?? '');

        if ($categoria_id <= 0) {
            $errors[] = 'Debes seleccionar una categoria';
        }
        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }
        if ($precio <= 0) {
            $errors[] = 'El precio debe ser mayor que 0';
        }
        if ($precio_oferta !== null && $precio_oferta > $precio) {
            $errors[] = 'El precio de oferta no puede ser mayor al precio';
        }
        if ($stock < 0) {
            $errors[] = 'El stock no puede ser negativo';
        }
        if ($imagen !== '' && !filter_var($imagen, FILTER_VALIDATE_URL)) {
            $errors[] = 'La URL de imagen no es valida';
        }

        $formData = [
            'categoria_id' => $categoria_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'precio_oferta' => $precio_oferta,
            'stock' => $stock,
            'imagen' => $imagen
        ];

        $redirect = $this->adminRequest->guardarErroresYRedirigir($errors, $formData, '/admin/productos/crear');
        if ($redirect) {
            $this->redirect($redirect);
            return;
        }

        $resultado = $this->productoRepository->create([
            'categoria_id' => $categoria_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'precio_oferta' => $precio_oferta,
            'stock' => $stock,
            'activo' => 1,
            'imagen' => $imagen ?: null
        ]);

        if ($resultado) {
            $this->adminRequest->guardarExito('Producto creado correctamente.');
            $this->redirect('/productos');
            return;
        }

        $redirect = $this->adminRequest->guardarErroresYRedirigir(['No se pudo crear el producto.'], $formData, '/admin/productos/crear');
        if ($redirect) {
            $this->redirect($redirect);
            return;
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos.'];
            $this->redirect('/');
            return;
        }

        $producto = $this->productoRepository->find((int) $id);

        if (!$producto) {
            $_SESSION['errors'] = ['El producto no existe.'];
            $this->redirect('/productos');
            return;
        }

        $data = [
            'title' => 'Editar Producto',
            'message' => 'Actualiza los datos del producto.',
            'producto' => $producto,
            'categorias' => $this->categoriaRepository->findAll(),
            'modo' => 'editar',
            'showHeader' => true,
            'showFooter' => true
        ];

        return $this->view('productos/form', $data);
    }

    public function update($id)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos.'];
            $this->redirect('/');
            return;
        }

        $producto = $this->productoRepository->find((int) $id);
        if (!$producto) {
            $_SESSION['errors'] = ['El producto no existe.'];
            $this->redirect('/productos');
            return;
        }

        $errors = [];
        $categoria_id = (int) ($_POST['categoria_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = (float) ($_POST['precio'] ?? 0);
        $precio_oferta = trim($_POST['precio_oferta'] ?? '') === '' ? null : (float) $_POST['precio_oferta'];
        $stock = (int) ($_POST['stock'] ?? 0);
        $imagen = trim($_POST['imagen'] ?? '');

        if ($categoria_id <= 0) {
            $errors[] = 'Debes seleccionar una categoria';
        }
        if (empty($nombre)) {
            $errors[] = 'El nombre es obligatorio';
        }
        if ($precio <= 0) {
            $errors[] = 'El precio debe ser mayor que 0';
        }
        if ($precio_oferta !== null && $precio_oferta > $precio) {
            $errors[] = 'El precio de oferta no puede ser mayor al precio';
        }
        if ($stock < 0) {
            $errors[] = 'El stock no puede ser negativo';
        }
        if ($imagen !== '' && !filter_var($imagen, FILTER_VALIDATE_URL)) {
            $errors[] = 'La URL de imagen no es valida';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = ['categoria_id' => $categoria_id, 'nombre' => $nombre, 'descripcion' => $descripcion, 'precio' => $precio, 'precio_oferta' => $precio_oferta, 'stock' => $stock, 'imagen' => $imagen];
            $this->redirect('/admin/productos/' . (int) $id . '/editar');
            return;
        }

        $resultado = $this->productoRepository->update((int) $id, [
            'categoria_id' => $categoria_id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
            'precio_oferta' => $precio_oferta,
            'stock' => $stock,
            'imagen' => $imagen ?: null
        ]);

        if ($resultado) {
            $_SESSION['success'] = 'Producto actualizado correctamente.';
            unset($_SESSION['form_data']);
            $this->redirect('/productos');
            return;
        }

        $_SESSION['errors'] = ['No se pudo actualizar el producto.'];
        $_SESSION['form_data'] = ['categoria_id' => $categoria_id, 'nombre' => $nombre, 'descripcion' => $descripcion, 'precio' => $precio, 'precio_oferta' => $precio_oferta, 'stock' => $stock, 'imagen' => $imagen];
        $this->redirect('/admin/productos/' . (int) $id . '/editar');
    }

    public function delete($id)
    {
        if (!$this->adminRequest->verificarPermisosAdmin()) {
            $this->redirect('/');
            return;
        }

        $resultado = $this->productoRepository->delete((int) $id);

        if ($resultado) {
            $this->adminRequest->guardarExito('Producto eliminado correctamente.');
        } else {
            $this->adminRequest->guardarError('No se pudo eliminar el producto.');
        }

        $this->redirect('/productos');
    }

}

