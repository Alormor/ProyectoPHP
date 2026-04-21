
-- ============================================================
-- Base de datos: tienda
-- ============================================================

CREATE DATABASE IF NOT EXISTS tienda
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tienda;

-- ------------------------------------------------------------
-- USUARIOS
-- ------------------------------------------------------------
CREATE TABLE `usuarios` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(60)      NOT NULL,
    `apellidos`   VARCHAR(60)      NOT NULL,
    `email`       VARCHAR(255)     NOT NULL,
    `password`    VARCHAR(255)     NOT NULL,   -- hash 
    `rol`         ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
    `confirmado`  BOOLEAN          NOT NULL DEFAULT FALSE,
    `token`       VARCHAR(255)     DEFAULT NULL,
    `token_exp`   DATETIME         DEFAULT NULL,
    `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuarios_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Categorías
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorias (
    id           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(100)     NOT NULL,
    descripcion  TEXT,
    created_at   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_categorias  PRIMARY KEY (id),
    CONSTRAINT uq_cat_nombre  UNIQUE (nombre)
    
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Productos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS productos (
    id             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    categoria_id   INT UNSIGNED     NOT NULL,
    nombre         VARCHAR(150)     NOT NULL,
    descripcion    TEXT,
    precio         DECIMAL(10,2)    NOT NULL CHECK (precio >= 0),
    precio_oferta  DECIMAL(10,2)    DEFAULT NULL,
    stock          INT UNSIGNED     NOT NULL DEFAULT 0,
    activo         TINYINT(1)       NOT NULL DEFAULT 1,
    imagen         VARCHAR(255),
    created_at     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP
                   ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_productos          PRIMARY KEY (id),
    CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id)
        REFERENCES categorias (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Índice para búsquedas por categoría y filtrado de activos
CREATE INDEX idx_productos_categoria ON productos (categoria_id, activo);

-- ------------------------------------------------------------
-- Pedidos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedidos (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    usuario_id    BIGINT UNSIGNED     NOT NULL,
    provincia     VARCHAR(100)     NOT NULL,
    localidad     VARCHAR(100)     NOT NULL,
    direccion     VARCHAR(255)     NOT NULL,
    subtotal      DECIMAL(12,2)    NOT NULL DEFAULT 0.00 CHECK (subtotal >= 0),
    impuestos     DECIMAL(12,2)    NOT NULL DEFAULT 0.00 CHECK (impuestos >= 0),
    coste_total   DECIMAL(12,2)    NOT NULL DEFAULT 0.00 CHECK (coste_total >= 0),
    estado        ENUM(
                    'pendiente',
                    'confirmado',
                    'pagado',
                    'enviado',
                    'entregado',
                    'cancelado'
                  ) NOT NULL DEFAULT 'pendiente',
    fecha_pedido  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_pedidos       PRIMARY KEY (id),
    CONSTRAINT fk_pedido_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Índice para consultar pedidos por usuario y estado
CREATE INDEX idx_pedidos_usuario ON pedidos (usuario_id, estado);

-- ------------------------------------------------------------
-- Líneas de pedido
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lineas_pedidos (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    pedido_id        INT UNSIGNED  NOT NULL,
    producto_id      INT UNSIGNED  NOT NULL,
    unidades         SMALLINT UNSIGNED NOT NULL CHECK (unidades > 0),
    precio_unitario  DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    subtotal_linea   DECIMAL(12,2)
	GENERATED ALWAYS AS (unidades * precio_unitario) STORED,
    CONSTRAINT pk_lineas_pedidos  PRIMARY KEY (id),
    CONSTRAINT fk_linea_pedido    FOREIGN KEY (pedido_id)
        REFERENCES pedidos (id)   ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_linea_producto  FOREIGN KEY (producto_id)
        REFERENCES productos (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- DATOS DE PRUEBA
-- ============================================================

-- USUARIOS DE PRUEBA
-- Contraseña admin: admin123456
-- Hash: $2y$12$4RHlZlJLJsM5QJ9.X6HXNuLX4G7kL8mQ2pP9zK1vB3cY0aZ8wX5qW
-- Contraseña usuario: usuario123456
-- Hash: $2y$12$7tK2pL9mN0oQ3rS4tU5vW6xY7zZ0aB1cD2eF3gH4iJ5kL6mN7oP8q

INSERT INTO `usuarios` (
    `nombre`, `apellidos`, `email`, `password`, `rol`, `confirmado`
) VALUES
-- Administrador
(
    'Juan',
    'García López',
    'admin@tienda.com',
    '$2y$12$4RHlZlJLJsM5QJ9.X6HXNuLX4G7kL8mQ2pP9zK1vB3cY0aZ8wX5qW',
    'admin',
    TRUE
),
-- Usuarios normales
(
    'María',
    'Rodríguez Martínez',
    'maria@email.com',
    '$2y$12$7tK2pL9mN0oQ3rS4tU5vW6xY7zZ0aB1cD2eF3gH4iJ5kL6mN7oP8q',
    'usuario',
    TRUE
),
(
    'Carlos',
    'López Fernández',
    'carlos@email.com',
    '$2y$12$7tK2pL9mN0oQ3rS4tU5vW6xY7zZ0aB1cD2eF3gH4iJ5kL6mN7oP8q',
    'usuario',
    TRUE
),
(
    'Ana',
    'Martínez García',
    'ana@email.com',
    '$2y$12$7tK2pL9mN0oQ3rS4tU5vW6xY7zZ0aB1cD2eF3gH4iJ5kL6mN7oP8q',
    'usuario',
    FALSE
),
(
    'Pedro',
    'Sánchez Ruiz',
    'pedro@email.com',
    '$2y$12$7tK2pL9mN0oQ3rS4tU5vW6xY7zZ0aB1cD2eF3gH4iJ5kL6mN7oP8q',
    'usuario',
    TRUE
);

-- CATEGORÍAS
INSERT INTO `categorias` (
    `nombre`, `descripcion`
) VALUES
(
    'Electrónica',
    'Dispositivos electrónicos y accesorios'
),
(
    'Libros',
    'Libros y material de lectura'
),
(
    'Ropa',
    'Prendas de vestir para hombre y mujer'
),
(
    'Hogar',
    'Artículos para el hogar'
),
(
    'Deportes',
    'Equipamiento deportivo'
);

-- PRODUCTOS
INSERT INTO `productos` (
    `categoria_id`, `nombre`, `descripcion`, `precio`, `precio_oferta`, `stock`, `activo`, `imagen`
) VALUES
-- Categoría Electrónica (id=1)
(
    1,
    'Laptop HP 15',
    'Laptop HP 15 pulgadas, procesador Intel i5, 8GB RAM, 256GB SSD',
    799.99,
    699.99,
    5,
    TRUE,
    'laptop-hp-15.jpg'
),
(
    1,
    'Mouse Inalámbrico',
    'Mouse inalámbrico con batería de larga duración',
    29.99,
    NULL,
    50,
    TRUE,
    'mouse-inalambrico.jpg'
),
(
    1,
    'Teclado Mecánico',
    'Teclado mecánico RGB con switches azules',
    89.99,
    79.99,
    12,
    TRUE,
    'teclado-mecanico.jpg'
),
-- Categoría Libros (id=2)
(
    2,
    'Don Quijote de la Mancha',
    'Novela clásica de Miguel de Cervantes',
    25.50,
    NULL,
    30,
    TRUE,
    'don-quijote.jpg'
),
(
    2,
    'El Quimico - Paulo Coelho',
    'Novela de aventura y transformación personal',
    18.99,
    16.99,
    25,
    TRUE,
    'el-quimico.jpg'
),
-- Categoría Ropa (id=3)
(
    3,
    'Camiseta Básica Blanca',
    'Camiseta de algodón 100% color blanco',
    14.99,
    NULL,
    100,
    TRUE,
    'camiseta-blanca.jpg'
),
(
    3,
    'Jeans Azul Premium',
    'Jeans azul oscuro con ajuste clásico',
    59.99,
    49.99,
    40,
    TRUE,
    'jeans-azul.jpg'
),
-- Categoría Hogar (id=4)
(
    4,
    'Lámpara LED de Escritorio',
    'Lámpara LED ajustable con control de intensidad',
    39.99,
    NULL,
    20,
    TRUE,
    'lampara-led.jpg'
),
(
    4,
    'Almohada Viscoelástica',
    'Almohada ergonómica de espuma viscoelástica',
    45.00,
    39.99,
    35,
    TRUE,
    'almohada-visco.jpg'
),
-- Categoría Deportes (id=5)
(
    5,
    'Balón de Fútbol Profesional',
    'Balón de fútbol tamaño oficial',
    35.99,
    NULL,
    15,
    TRUE,
    'balon-futbol.jpg'
),
(
    5,
    'Zapatillas Running Nike',
    'Zapatillas deportivas para correr',
    129.99,
    99.99,
    22,
    TRUE,
    'zapatillas-nike.jpg'
);

-- PEDIDOS
-- Usuario María (id=2)
INSERT INTO `pedidos` (
    `usuario_id`, `provincia`, `localidad`, `direccion`, `subtotal`, `impuestos`, `coste_total`, `estado`, `fecha_pedido`
) VALUES
(
    2,
    'Madrid',
    'Madrid',
    'Calle Principal 123, Apt 4B',
    799.99,
    160.00,
    959.99,
    'entregado',
    '2026-04-10 10:30:00'
),
-- Usuario Carlos (id=3)
(
    3,
    'Barcelona',
    'Barcelona',
    'Avenida Paseo de Gracia 456',
    129.98,
    25.99,
    155.97,
    'pagado',
    '2026-04-15 14:15:00'
),
-- Usuario Pedro (id=5)
(
    5,
    'Valencia',
    'Valencia',
    'Plaza del Mercado 789',
    84.98,
    16.99,
    101.97,
    'confirmado',
    '2026-04-18 09:45:00'
);

-- LÍNEAS DE PEDIDOS
-- Pedido 1 (María) - Laptop
INSERT INTO `lineas_pedidos` (
    `pedido_id`, `producto_id`, `unidades`, `precio_unitario`
) VALUES
(
    1,
    1,
    1,
    699.99
);

-- Pedido 2 (Carlos) - Teclado y Mouse
INSERT INTO `lineas_pedidos` (
    `pedido_id`, `producto_id`, `unidades`, `precio_unitario`
) VALUES
(
    2,
    2,
    2,
    29.99
),
(
    2,
    3,
    1,
    79.99
);

-- Pedido 3 (Pedro) - Libro y Camiseta
INSERT INTO `lineas_pedidos` (
    `pedido_id`, `producto_id`, `unidades`, `precio_unitario`
) VALUES
(
    3,
    4,
    2,
    25.50
),
(
    3,
    6,
    3,
    14.99
);




