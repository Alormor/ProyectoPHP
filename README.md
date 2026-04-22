# Proyecto PHP - Tienda Online

Sistema de gestión de tienda online con arquitectura MVC implementada en PHP puro, sin frameworks.

## Tabla de Contenidos

1. [Arquitectura](#arquitectura)
2. [Estructura de Carpetas](#estructura-de-carpetas)
3. [Características Implementadas](#características-implementadas)
4. [Sistema de Autenticación y Registro](#sistema-de-autenticación-y-registro)
5. [Rutas Amigables (URLs limpias)](#rutas-amigables)
6. [Flujo de Datos](#flujo-de-datos)
7. [Base de Datos](#base-de-datos)
8. [Instalación y Configuración](#instalación-y-configuración)

---

## Arquitectura

Se implementa una arquitectura en capas con responsabilidad única (SOLID):

```
Presentación (Views)
       ↓
Controladores (Controllers)
       ↓
Servicios (Services)
       ↓
Repositorios (Repositories)
       ↓
Base de Datos
```

### Capas del Proyecto

**1. Controladores (Controllers)**
- Reciben las solicitudes HTTP
- Delegan lógica de negocio al Servicio
- Retornan vistas al usuario
- Gestionan sesiones y redirecciones

**2. Servicios (Services)**
- Contienen la lógica de negocio
- Coordinan procesos complejos
- Validan reglas de negocio
- Delegan persistencia a Repositorios

**3. Repositorios (Repositories)**
- Única capa que accede a base de datos
- Usan prepared statements para seguridad
- Evitan inyección SQL
- Retornan datos estructurados

**4. Request/Validadores**
- Validan datos de entrada
- Sanitizan información para prevenir XSS
- Aplican reglas estrictas de validación

**5. Vistas (Views)**
- Presentan datos al usuario
- Divididas en subcarpetas por entidad
- Incluyen componentes compartidos (header, footer)

---

## Estructura de Carpetas

```
ProyectoPHP/
├── public/                      # Punto de entrada, archivos estáticos
│   ├── index.php               # Puerta principal de la aplicación
│   ├── css/                    # Hojas de estilo
│   ├── js/                     # JavaScript
│   ├── images/                 # Imágenes
│   └── .htaccess               # Reescritura de URLs
├── src/                        # Código fuente
│   ├── Controllers/            # Controladores
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── UsuarioController.php
│   │   ├── PedidoController.php
│   │   ├── CategoriaController.php
│   │   ├── ProductoController.php
│   │   ├── CarritoController.php
│   │   └── ErrorController.php
│   ├── Services/               # Lógica de negocio
│   │   ├── Service.php         # Clase base
│   │   └── UsuarioService.php
│   ├── Repositories/           # Acceso a datos
│   │   ├── Repository.php      # Clase base
│   │   └── UsuarioRepository.php
│   ├── Models/                 # Modelos de datos
│   │   ├── Model.php
│   │   ├── Usuario.php
│   │   ├── Pedido.php
│   │   ├── Categoria.php
│   │   └── Producto.php
│   ├── Request/                # Validación de entrada
│   │   ├── Request.php
│   │   └── UserRequest.php
│   ├── Core/                   # Core de la aplicación
│   │   ├── Application.php
│   │   ├── Controller.php
│   │   └── Router.php
│   ├── Database/               # Configuración BD
│   │   └── database.sql
│   ├── Views/                  # Vistas HTML
│   │   ├── home.php
│   │   ├── shared/            # Componentes compartidos
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   ├── usuarios/          # Vistas de usuarios
│   │   │   ├── index.php
│   │   │   ├── formregistro.php
│   │   │   ├── formlogin.php
│   │   │   └── formcreate.php
│   │   ├── pedidos/
│   │   ├── categorias/
│   │   └── productos/
│   └── .htaccess               # Seguridad de carpeta
├── config/                     # Configuración general
├── vendor/                     # Dependencias composer
├── .gitignore
├── .htaccess                   # Reescritura raíz
└── README.md
```

---

## Características Implementadas

### 1. ✅ Sistema de Rutas Amigables (URLs Limpias)

**Implementación:**
- Módulo `mod_rewrite` de Apache habilitado
- Tres archivos `.htaccess` estratégicamente ubicados:
  - **Raíz**: Bloquea carpetas sensibles, redirige a `public/`
  - **Public**: Redirige todas las URLs a `index.php`
  - **Src**: Deniega acceso directo

**Ejemplo de rutas:**
```
Antes:  /public/index.php?uri=productos
Ahora:  /productos

Antes:  /public/index.php?uri=usuarios/123/editar
Ahora:  /usuarios/123/editar
```

**Configuración requerida:**
- `mod_rewrite` activo en Apache
- `AllowOverride All` en la configuración de Apache

### 2. ✅ Sistema de Autenticación y Registro

**Dos flujos diferentes:**

#### a) Auto-registro de usuarios
- Usuarios normales se registran en `/registro`
- Rol asignado automáticamente: **'user'**
- Estado: **no confirmado** (requiere verificación de email)
- Contraseña: encriptada con bcrypt

#### b) Creación por administrador
- Solo usuarios con rol **'admin'** acceden a `/admin/usuarios/crear`
- Pueden crear usuarios con rol:
  - **'user'** (usuario normal)
  - **'admin'** (administrador)
- Usuario creado: **confirmado automáticamente**
- No requiere verificación de email

**Flujo técnico:**

```
        ┌─ Auto-registro (Usuario) ─────────┐
        │                                   │
    Solicitud HTTP                    /registro/save
        │                                   │
        ↓                                   ↓
   AuthController                    UserRequest
   - register()                    - sanitize()
   - save()                        - validateUser()
        │                                   │
        └──────────────────────────────────┘
                     │
                     ↓
            UsuarioService
              - registrar()
                  │
              ✓ Verificar email
              ✓ Encriptar contraseña
              ✓ Rol = 'user'
              ✓ Confirmado = false
                  │
                  ↓
        UsuarioRepository
           - create()
          (INSERT en BD)
                  │
                  ↓
            Sesión + Redirect
            (/login)
```

**Para Administrador:**

```
    /admin/usuarios/crear ──→ AuthController->create() [Verifica rol admin]
                                      │
                                      ↓
                            Muestra formulario
                            (formcreate.php)
                                      │
                                      ↓
                            Admin completa datos
                            Elige rol (user/admin)
                                      │
                                      ↓
                            /admin/usuarios ──→ AuthController->store()
                                      │
                                      ↓
                            UserRequest
                          - sanitize()
                          - validateAdmin()
                            (valida rol)
                                      │
                                      ↓
                            UsuarioService->crear()
                              ✓ Verifica admin
                              ✓ Encripta contraseña
                              ✓ Rol = seleccionado
                              ✓ Confirmado = true
                                      │
                                      ↓
                            UsuarioRepository->create()
```

### 3. ✅ Validación y Sanitización

**UserRequest:**
- **Sanitización**: Limpia datos XSS, espacios en blanco
- **Validación**: Reglas estrictas según contexto

**Reglas de validación:**

#### Para auto-registro (validateUser):
- Nombre: requerido, no vacío
- Apellidos: requerido, no vacío
- Email: requerido, formato válido, único (TODO)
- Contraseña: mín 8 caracteres
- Confirmación: debe coincidir

#### Para admin (validateAdmin):
- Todos los campos anteriores +
- Rol: obligatorio, debe ser 'user' o 'admin'

**Ejemplo de sanitización:**

```php
Input: "<script>alert('XSS')</script>"
Output: "&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;"

Input: "  Juan  "
Output: "Juan"

Input: "USUARIO@EMAIL.COM"
Output: "usuario@email.com"
```

---

## Sistema de Autenticación y Registro

### Componentes Principales

#### 1. **AuthController** (`src/Controllers/AuthController.php`)
Controlador responsable de todas las acciones de autenticación.

**Métodos:**
- `register()` - Renderiza formulario de auto-registro
- `save()` - Procesa auto-registro de usuarios
- `create()` - Renderiza formulario de creación por admin (verifica permisos)
- `store()` - Procesa creación de usuario por admin (verifica permisos)
- `login()` - Renderiza formulario de login
- `logout()` - Destruye sesión

#### 2. **UserRequest** (`src/Request/UserRequest.php`)
Valida y sanitiza datos de entrada.

**Métodos:**
- `sanitize()` - Limpia datos de entrada
- `validate_and_sanitize($tipo)` - Valida según tipo ('usuario' o 'admin')
- `validateUser()` - Validación para auto-registro
- `validateAdmin()` - Validación para creación por admin
- `getErrors()` - Retorna array de errores
- `getSanitized()` - Retorna datos limpios

#### 3. **UsuarioService** (`src/Services/UsuarioService.php`)
Lógica de negocio de usuarios.

**Métodos:**
- `registrar($userData)` - Auto-registro de usuarios
  - Encripta contraseña (bcrypt, cost=12)
  - Rol automático: 'user'
  - Confirmado: false
  - Delega al repositorio
  
- `crear($userData, $adminId)` - Creación por admin
  - Verifica que admin existe y tiene rol 'admin'
  - Permite elegir rol
  - Confirmado: true
  - Delega al repositorio

#### 4. **UsuarioRepository** (`src/Repositories/UsuarioRepository.php`)
Acceso a base de datos.

**Métodos:**
- `create($data)` - INSERT usuario (TODO: implementar)
- `findByEmail($email)` - Buscar por email (TODO: implementar)
- `find($id)` - Buscar por ID (TODO: implementar)
- `update($id, $data)` - Actualizar (TODO: implementar)
- `delete($id)` - Eliminar (TODO: implementar)

### Seguridad Implementada

1. **Encriptación de contraseña**
   - Algoritmo: bcrypt
   - Cost: 12 (computacionalmente seguro)
   - Genera hash único incluso para misma contraseña

2. **Sanitización de entrada**
   - XSS prevention con `htmlspecialchars()`
   - Espacios en blanco eliminados
   - Emails convertidos a minúsculas

3. **Validación estricta**
   - Formato de email validado
   - Longitud mínima de contraseña (8 caracteres)
   - Confirmación de contraseña requerida
   - Rol validado (solo 'user' o 'admin')

4. **Control de acceso**
   - Admins verificados por rol en sesión
   - Métodos protegidos (create, store)
   - Redirecciones a inicio si no autorizado

5. **Prepared Statements** (TODO)
   - Previenen inyección SQL
   - Parámetros separados de consulta

---

## Rutas Amigables

### Configuración de `.htaccess`

**Raíz (.htaccess):**
```apache
RewriteEngine On
RewriteBase /ProyectoPHP/

# Bloquear acceso a carpetas sensibles
RewriteRule ^vendor/ - [F,L]
RewriteRule ^config/ - [F,L]
RewriteRule ^src/ - [F,L]

# Redirigir a public
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/index.php [QSA,L]
```

**Public (.htaccess):**
```apache
RewriteEngine On
RewriteBase /ProyectoPHP/public/

# Permitir archivos/directorios existentes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Reescribir a index.php
RewriteRule ^(.*)$ index.php?uri=$1 [QSA,L]
```

**Src (.htaccess):**
```apache
Order allow,deny
Deny from all
```

### Flujo de una Petición

1. Usuario accede: `http://localhost/ProyectoPHP/productos`
2. Apache recibe la petición
3. `.htaccess` raíz: verifica que no sea archivo real
4. Redirige a: `public/index.php?uri=productos`
5. `.htaccess` public: verifica nuevamente
6. Router procesa: `/productos`
7. Ejecuta controlador y acción correspondiente

---

## Flujo de Datos

### Auto-registro de usuario

```
1. Usuario accede /registro
   ↓
2. AuthController->register() renderiza formregistro.php
   ↓
3. Usuario completa formulario y envía
   ↓
4. POST a /registro/save
   ↓
5. AuthController->save()
   ├─ Crea UserRequest
   ├─ Valida y sanitiza (tipo='usuario')
   │  ├─ Si falla: guarda errores en $_SESSION['errors']
   │  └─ Redirige a /registro
   ├─ Si pasa: obtiene datos limpios
   │
6. Crea UsuarioService
   ├─ Llama registrar($userData)
   ├─ Encripta contraseña
   ├─ Define rol='user', confirmado=false
   ├─ Crea UsuarioRepository
   ├─ Delega create() al repositorio
   └─ Repositorio inserta en BD y retorna ID
   ↓
7. Si resultado OK:
   ├─ $_SESSION['register'] = 'success'
   ├─ Redirige a /login
   └─ Usuario puede iniciar sesión
   ↓
8. Si resultado ERROR:
   ├─ $_SESSION['errors'] = [mensaje error]
   └─ Redirige a /registro
```

### Creación de usuario por admin

```
1. Admin accede /admin/usuarios/crear
   ↓
2. AuthController->create()
   ├─ Verifica $_SESSION['usuario']['rol'] === 'admin'
   └─ Si no admin: redirige a /
   ↓
3. Renderiza formcreate.php (con campo rol)
   ↓
4. Admin selecciona rol (user/admin) y envía
   ↓
5. POST a /admin/usuarios
   ↓
6. AuthController->store()
   ├─ Verifica nuevamente rol admin
   ├─ Crea UserRequest
   ├─ Valida y sanitiza (tipo='admin')
   │  ├─ Valida rol: debe ser 'user' o 'admin'
   │  └─ Si falla: guarda errores
   ├─ Si pasa: obtiene datos
   │
7. Crea UsuarioService
   ├─ Llama crear($userData, adminId)
   ├─ Verifica que admin existe y tiene rol 'admin'
   ├─ Encripta contraseña
   ├─ Usa rol seleccionado, confirmado=true
   ├─ Crea UsuarioRepository
   └─ Delega create() al repositorio
   ↓
8. Si resultado OK:
   ├─ $_SESSION['success'] = 'Usuario creado...'
   └─ Redirige a /admin/usuarios
   ↓
9. Si resultado ERROR:
   ├─ $_SESSION['errors'] = [mensaje error]
   └─ Redirige a /admin/usuarios/crear
```

---

## Base de Datos

### Tabla `usuarios`

```sql
CREATE TABLE `usuarios` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(60)      NOT NULL,
    `apellidos`   VARCHAR(60)      NOT NULL,
    `email`       VARCHAR(255)     NOT NULL UNIQUE,
    `password`    VARCHAR(255)     NOT NULL,   -- hash bcrypt
    `rol`         ENUM('admin','user') NOT NULL DEFAULT 'user',
    `confirmado`  BOOLEAN          NOT NULL DEFAULT FALSE,
    `token`       VARCHAR(255)     DEFAULT NULL,
    `token_exp`   DATETIME         DEFAULT NULL,
    `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuarios_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Propiedades del Usuario

Después de guardar en BD, se conocen:
- `id` - ID único generado
- `rol` - Asignado según contexto
- `confirmado` - true/false según tipo de registro
- `created_at` - Timestamp de creación
- `updated_at` - Timestamp de última actualización

---

## Instalación y Configuración

### Requisitos

- PHP 7.4+
- Apache con `mod_rewrite` activo
- MySQL 5.7+
- Composer

### Pasos de instalación

1. **Clonar/descargar proyecto**
   ```bash
   git clone <repo-url>
   cd ProyectoPHP
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar base de datos**
   ```bash
   # Crear base de datos
   mysql -u root -p < src/Database/database.sql
   ```

4. **Configurar Apache**
   - Verificar que `mod_rewrite` está activo
   - Configurar `AllowOverride All` para el directorio

5. **Crear archivo `.env` (si aplica)**
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=tienda
   ```

6. **Acceder a la aplicación**
   ```
   http://localhost/ProyectoPHP/
   ```

---

## TODO

- [ ] Implementar conexión a base de datos en Repositorios
- [ ] Implementar autenticación (login/logout)
- [ ] Verificación de email para nuevos usuarios
- [ ] Recovery de contraseña
- [ ] CRUD completo para todas las entidades
- [ ] Sistema de carrito
- [ ] Gestión de pedidos
- [ ] Panel de administración
- [ ] Validación de stock en productos
- [ ] Sistema de búsqueda
- [ ] Paginación
- [ ] Logging de errores

---

## Licencia

Proyecto educativo para aprendizaje de PHP y arquitectura de software.
