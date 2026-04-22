<?php

namespace Controllers;

use Core\Controller;
use Repositories\UsuarioRepository;

class DebugController extends Controller
{
    public function usuarios()
    {
        // SOLO PARA DEBUG - ELIMINAR EN PRODUCCIÓN
        try {
            $repo = new UsuarioRepository();
            
            // Obtener todos los usuarios
            $usuarios = $repo->findAll();
            
            if (empty($usuarios)) {
                $this->json(['error' => 'No hay usuarios en la BD']);
                return;
            }
            
            // Mostrar usuarios (sin contraseñas por seguridad)
            $usuariosInfo = [];
            foreach ($usuarios as $usuario) {
                $usuariosInfo[] = [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'apellidos' => $usuario['apellidos'],
                    'email' => $usuario['email'],
                    'rol' => $usuario['rol'],
                    'confirmado' => $usuario['confirmado'],
                    'password_hash_preview' => substr($usuario['password'], 0, 20) . '...'
                ];
            }
            
            $this->json([
                'total_usuarios' => count($usuariosInfo),
                'usuarios' => $usuariosInfo,
                'debug_info' => 'Esto es DEBUG. ELIMINAR EN PRODUCCIÓN'
            ]);
            
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
?>
