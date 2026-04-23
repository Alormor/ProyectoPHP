<?php

namespace Request;

class UserRequest extends Request
{
    private $errors = [];
    
    private $sanitized = [];
    
    public function sanitize()
    {
        // Obtener datos del POST
        $data = $this->post('data', []);
        
        // Sanitizar nombre
        if (isset($data['nombre'])) {
            $this->sanitized['nombre'] = trim(htmlspecialchars($data['nombre'], ENT_QUOTES, 'UTF-8'));
        }
        
        // Sanitizar apellidos
        if (isset($data['apellidos'])) {
            $this->sanitized['apellidos'] = trim(htmlspecialchars($data['apellidos'], ENT_QUOTES, 'UTF-8'));
        }
        
        // Sanitizar email
        if (isset($data['email'])) {
            $this->sanitized['email'] = trim(strtolower($data['email']));
        }
        
        // La contraseña no se sanitiza con htmlspecialchars, solo se trimea
        if (isset($data['password'])) {
            $this->sanitized['password'] = trim($data['password']);
        }
        
        // Confirmar contraseña
        if (isset($data['password_confirm'])) {
            $this->sanitized['password_confirm'] = trim($data['password_confirm']);
        }
        
        // Sanitizar rol (solo si viene en los datos)
        if (isset($data['rol'])) {
            $this->sanitized['rol'] = trim(strtolower($data['rol']));
        }
        
        return $this->sanitized;
    }
    

    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getSanitized()
    {
        return $this->sanitized;
    }
    
    public function validate_and_sanitize($tipo = 'usuario')
    {
        $this->sanitize();
        return $this->validate($tipo);
    }
    
    public function validate($tipo = 'usuario')
    {
        if ($tipo === 'admin') {
            return $this->validateAdmin();
        }
        
        return $this->validateUser();
    }
    
    public function validateUser()
    {
        if (empty($this->sanitized['email'])) {
            $this->errors[] = 'El email es requerido';
        } else {
            if (!filter_var($this->sanitized['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = 'El formato del email no es válido';
            }
        }
        
        if (empty($this->sanitized['password'])) {
            $this->errors[] = 'La contraseña es requerida';
        } else {
            if (strlen($this->sanitized['password']) < 8) {
                $this->errors[] = 'La contraseña debe tener al menos 8 caracteres';
            }
        }
        
        if (empty($this->sanitized['password_confirm'])) {
            $this->errors[] = 'Debe confirmar la contraseña';
        }
        
        if (!empty($this->sanitized['password']) && !empty($this->sanitized['password_confirm'])) {
            if ($this->sanitized['password'] !== $this->sanitized['password_confirm']) {
                $this->errors[] = 'Las contraseñas no coinciden';
            }
        }
        
        return empty($this->errors);
    }
    
    public function validateAdmin()
    {
        // Validación para creación de usuarios por administrador
        if (empty($this->sanitized['nombre'])) {
            $this->errors[] = 'El nombre es requerido';
        }
        
        if (empty($this->sanitized['apellidos'])) {
            $this->errors[] = 'Los apellidos son requeridos';
        }
        
        if (empty($this->sanitized['email'])) {
            $this->errors[] = 'El email es requerido';
        } else {
            if (!filter_var($this->sanitized['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = 'El formato del email no es válido';
            }
        }
        
        if (empty($this->sanitized['password'])) {
            $this->errors[] = 'La contraseña es requerida';
        } else {
            if (strlen($this->sanitized['password']) < 8) {
                $this->errors[] = 'La contraseña debe tener al menos 8 caracteres';
            }
        }
        
        if (empty($this->sanitized['password_confirm'])) {
            $this->errors[] = 'Debe confirmar la contraseña';
        }
        
        if (!empty($this->sanitized['password']) && !empty($this->sanitized['password_confirm'])) {
            if ($this->sanitized['password'] !== $this->sanitized['password_confirm']) {
                $this->errors[] = 'Las contraseñas no coinciden';
            }
        }
        
        // Validación adicional para rol (solo para admins)
        if (empty($this->sanitized['rol'])) {
            $this->errors[] = 'El rol es requerido';
        } else {
            if (!in_array($this->sanitized['rol'], ['usuario', 'admin'])) {
                $this->errors[] = 'El rol debe ser "usuario" o "admin"';
            }
        }
        
        return empty($this->errors);
    }
}
?>
