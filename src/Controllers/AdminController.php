<?php

namespace Controllers;

use Core\Controller;
use Repositories\ProductoRepository;
use Repositories\CategoriaRepository;
use Repositories\UsuarioRepository;

/**
 * Controlador principal de administración
 * Contiene toda la lógica base y métodos de gestión del sistema
 */
class AdminController extends Controller
{
    protected $repository;
    protected $productoRepository;
    protected $categoriaRepository;
    protected $usuarioRepository;

    public function __construct()
    {
        $this->productoRepository = new ProductoRepository();
        $this->categoriaRepository = new CategoriaRepository();
        $this->usuarioRepository = new UsuarioRepository();
    }

    // ========== MÉTODOS BASE PARA ADMINISTRACIÓN ==========

    /**
     * Verifica si el usuario tiene permisos de admin
     */
    protected function verificarPermisosAdmin()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para realizar esta acción.'];
            $this->redirect('/');
            return false;
        }
        return true;
    }

    /**
     * Prepara los datos de la sesión para mostrar en la vista
     */
    protected function prepararDatosVista($title, $message, $data = [])
    {
        return array_merge([
            'title' => $title,
            'message' => $message,
            'showHeader' => true,
            'showFooter' => true
        ], $data);
    }

    /**
     * Guarda errores en sesión y redirige
     */
    protected function guardarErroresYRedirigir($errors, $formData, $redirectUrl)
    {
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $formData;
            $this->redirect($redirectUrl);
            return;
        }
    }

    /**
     * Guarda un mensaje de éxito en sesión
     */
    protected function guardarExito($mensaje)
    {
        $_SESSION['success'] = $mensaje;
        unset($_SESSION['form_data']);
    }

    /**
     * Guarda un error en sesión
     */
    protected function guardarError($mensaje)
    {
        $_SESSION['errors'] = [$mensaje];
    }

    /**
     * Limpia datos de formulario de la sesión
     */
    protected function limpiarFormData()
    {
        if (isset($_SESSION['form_data'])) {
            unset($_SESSION['form_data']);
        }
    }

    // ========== MÉTODOS DEL PANEL ADMINISTRATIVO ==========

    /**
     * Muestra el dashboard principal del admin
     */
    public function dashboard()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        try {
            $totalProductos = count($this->productoRepository->findAll());
            $totalCategorias = count($this->categoriaRepository->findAll());
            $totalUsuarios = count($this->usuarioRepository->findAll());

            $ultimosProductos = array_slice($this->productoRepository->findAll(), -5);
            $ultimosCategorias = array_slice($this->categoriaRepository->findAll(), -5);

            $data = $this->prepararDatosVista(
                'Panel de Administración',
                'Bienvenido al panel administrativo',
                [
                    'totalProductos' => $totalProductos,
                    'totalCategorias' => $totalCategorias,
                    'totalUsuarios' => $totalUsuarios,
                    'ultimosProductos' => $ultimosProductos,
                    'ultimosCategorias' => $ultimosCategorias
                ]
            );

            return $this->view('admin/dashboard', $data);
        } catch (\Exception $e) {
            $this->guardarError('Error al cargar el dashboard: ' . $e->getMessage());
            $this->redirect('/');
            return;
        }
    }

    /**
     * Muestra estadísticas generales
     */
    public function estadisticas()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        try {
            $productos = $this->productoRepository->findAll();
            $categorias = $this->categoriaRepository->findAll();
            $usuarios = $this->usuarioRepository->findAll();

            // Calcular estadísticas
            $productosActivos = count(array_filter($productos, fn($p) => $p['activo'] == 1));
            $productosInactivos = count($productos) - $productosActivos;
            $productosAgotados = count(array_filter($productos, fn($p) => $p['stock'] == 0));

            $usuariosAdmins = count(array_filter($usuarios, fn($u) => $u['rol'] === 'admin'));
            $usuariosNormales = count($usuarios) - $usuariosAdmins;

            $data = $this->prepararDatosVista(
                'Estadísticas',
                'Resumen de estadísticas del sistema',
                [
                    'totalProductos' => count($productos),
                    'productosActivos' => $productosActivos,
                    'productosInactivos' => $productosInactivos,
                    'productosAgotados' => $productosAgotados,
                    'totalCategorias' => count($categorias),
                    'totalUsuarios' => count($usuarios),
                    'usuariosAdmins' => $usuariosAdmins,
                    'usuariosNormales' => $usuariosNormales
                ]
            );

            return $this->view('admin/estadisticas', $data);
        } catch (\Exception $e) {
            $this->guardarError('Error al cargar estadísticas: ' . $e->getMessage());
            $this->redirect('/');
            return;
        }
    }

    /**
     * Muestra la página de configuración
     */
    public function configuracion()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $data = $this->prepararDatosVista(
            'Configuración',
            'Configuración general del sistema'
        );

        return $this->view('admin/configuracion', $data);
    }

    /**
     * Guarda cambios de configuración
     */
    public function guardarConfiguracion()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $this->guardarExito('Configuración guardada correctamente.');
        $this->redirect('/admin/configuracion');
    }

    /**
     * Muestra reportes
     */
    public function reportes()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        try {
            $productos = $this->productoRepository->findAll();

            // Agrupar productos por categoría
            $productosPorCategoria = [];
            foreach ($productos as $producto) {
                $catId = $producto['categoria_id'];
                if (!isset($productosPorCategoria[$catId])) {
                    $productosPorCategoria[$catId] = [];
                }
                $productosPorCategoria[$catId][] = $producto;
            }

            // Obtener nombres de categorías
            $categorias = $this->categoriaRepository->findAll();
            $categoriasPorId = array_column($categorias, 'nombre', 'id');

            $data = $this->prepararDatosVista(
                'Reportes',
                'Reportes y análisis del sistema',
                [
                    'productosPorCategoria' => $productosPorCategoria,
                    'categoriasPorId' => $categoriasPorId
                ]
            );

            return $this->view('admin/reportes', $data);
        } catch (\Exception $e) {
            $this->guardarError('Error al cargar reportes: ' . $e->getMessage());
            $this->redirect('/');
            return;
        }
    }
}
?>
