<?php

namespace Controllers;

use Core\Controller;

class UsuarioController extends Controller
{

    public function index()
    {
        // Listar todos los usuarios
    }
    
    public function show($id)
    {
        $data = [
            'title' => 'Detalle de Usuario',
            'message' => "Mostrando detalles del usuario con ID: $id",
            'showHeader' => true,
            'showFooter' => true
        ];
        return $this->view('usuarios/userprofile', $data);
    }
    
    public function create()
    {
        // Solo los administradores pueden crear usuarios
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para crear usuarios.'];
            $this->redirect('/');
            return;
        }
        
        $data = [
            'title' => 'Crear Usuario',
            'message' => 'Crear nueva cuenta de usuario',
            'es_admin' => true,
            'showHeader' => true,
            'showFooter' => true
        ];
        
        return $this->view('usuarios/formcreate', $data);
    }
    
    
    public function store()
    {
        // Guardar un nuevo usuario
    }
    
    public function edit($id)
    {
        // Mostrar formulario de edición
    }
    
    public function update($id)
    {
        // Actualizar un usuario
    }
    
    public function delete($id)
    {
        // Eliminar un usuario
    }
}
