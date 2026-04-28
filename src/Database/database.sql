
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
    `nombre`      VARCHAR(60)      ,
    `apellidos`   VARCHAR(60)      ,
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
-- Contraseña admin: admin

INSERT INTO `usuarios` (
    `nombre`, `apellidos`, `email`, `password`, `rol`, `confirmado`
) VALUES
-- Administrador
-- contraseña= admin123456
(
    'Juan',
    'García López',
    'admin@tienda.com',
    '$2y$12$qGSvq.kiP3g3OQd44vTFiuK8W/KqJQYlVSN2P9j0lXE8n6.YHSpSi',
    'admin',
    TRUE
),
-- Usuario test
(
    'Test',
    'Admin',
    'test@tienda.com',
    '$2y$12$qGSvq.kiP3g3OQd44vTFiuK8W/KqJQYlVSN2P9j0lXE8n6.YHSpSi',
    'admin',
    TRUE
),
-- Usuario normal
(
    'María',
    'Rodríguez Martínez',
    'maria@email.com',
    '$2y$12$7tK2pL9mN0oQ3rS4tU5vW6xY7zZ0aB1cD2eF3gH4iJ5kL6mN7oP8q',
    'usuario',
    TRUE
);



-- CATEGORÍAS
INSERT INTO `categorias` (nombre, descripcion) VALUES
('Cubos 2x2', 'Cubos de Rubik 2x2x2'),
('Cubos 3x3', 'Cubos de Rubik estándar 3x3x3'),
('Cubos 4x4', 'Cubos de Rubik 4x4x4'),
('Cubos 5x5', 'Cubos de Rubik 5x5x5'),
('Cubos Piramide', 'Pirámides y otros formatos'),
('Accesorios', 'Lubricantes, soportes y más');

-- PRODUCTOS
INSERT INTO `productos` (categoria_id, nombre, descripcion, precio, precio_oferta, stock, activo, imagen) VALUES
-- Cubos 2x2
(1, 'Rubiks Cube 2x2 Clásico', 'El famoso cubo 2x2 de Rubiks. Perfecto para principiantes.', 14.99, 11.99, 25, 1, 'https://placehold.co/300x300/e63946/ffffff?text=Rubiks+2x2'),
(1, 'MoYu Lingao 2x2', 'Cubo 2x2 profesional de alta velocidad. Muy fluido y rápido.', 8.99, NULL, 40, 1, 'https://placehold.co/300x300/457b9d/ffffff?text=MoYu+2x2'),
(1, 'QiYi Qidi 2x2', 'Cubo 2x2 de presupuesto con buen desempeño.', 5.99, 4.99, 50, 1, 'https://placehold.co/300x300/2a9d8f/ffffff?text=QiYi+2x2'),
-- Cubos 3x3
(2, 'Rubiks Cube 3x3 Original', 'El icónico cubo de Rubik 3x3x3. El clásico de los clásicos.', 24.99, 19.99, 35, 1, 'https://placehold.co/300x300/e63946/ffffff?text=Rubiks+3x3'),
(2, 'Gan 12 Maglev', 'El mejor cubo 3x3 del mercado. Competitivo profesional. Imanes magnéticos.', 59.99, 49.99, 15, 1, 'https://placehold.co/300x300/6a0572/ffffff?text=Gan+12+Maglev'),
(2, 'Moyu RS3M 2020', 'Excelente cubo 3x3 con magnets. Relación calidad-precio insuperable.', 19.99, 16.99, 45, 1, 'https://placehold.co/300x300/457b9d/ffffff?text=Moyu+RS3M'),
(2, 'Yuxin Little Magic 3x3', 'Cubo económico pero muy competitivo. Ideal para aprender.', 7.99, 5.99, 60, 1, 'https://placehold.co/300x300/2a9d8f/ffffff?text=Yuxin+3x3'),
-- Cubos 4x4
(3, 'Rubiks Cube 4x4 Master', 'El cubo 4x4 de Rubiks. Gran desafío. Requiere más habilidad.', 34.99, 27.99, 20, 1, 'https://placehold.co/300x300/e76f51/ffffff?text=Rubiks+4x4'),
(3, 'Gan 460M', 'Cubo 4x4 profesional con magnets. Ultra rápido y fluido.', 49.99, 39.99, 12, 1, 'https://placehold.co/300x300/6a0572/ffffff?text=Gan+460M'),
(3, 'Moyu AoSu GTS3M', 'Cubo 4x4 con magnets de competición. Excelente control.', 29.99, 24.99, 18, 1, 'https://placehold.co/300x300/457b9d/ffffff?text=Moyu+AoSu'),
-- Cubos 5x5
(4, 'Rubiks Cube 5x5 Professor', 'El desafiante cubo 5x5 de Rubiks. Máxima dificultad.', 49.99, 39.99, 10, 1, 'https://placehold.co/300x300/e63946/ffffff?text=Rubiks+5x5'),
(4, 'Gan 12M 5x5', 'Cubo 5x5 profesional con magnets. De los mejores del mercado.', 79.99, 64.99, 8, 1, 'https://placehold.co/300x300/6a0572/ffffff?text=Gan+12M+5x5'),
(4, 'Moyu Aochuang GTS 5x5', 'Cubo 5x5 competitivo con magnets. Muy controlable.', 34.99, 27.99, 14, 1, 'https://placehold.co/300x300/2a9d8f/ffffff?text=Moyu+5x5'),
-- Cubos Pirámide y otros
(5, 'Pirámide Rubiks', 'El clásico cubo pirámide. Forma diferente, reto interesante.', 22.99, 17.99, 22, 1, 'https://placehold.co/300x300/f4a261/ffffff?text=Piramide'),
(5, 'Cubo Espejo', 'Cubo con dimensiones diferentes en cada lado. Engañosamente difícil.', 18.99, 14.99, 18, 1, 'https://placehold.co/300x300/264653/ffffff?text=Cubo+Espejo'),
(5, 'Skewb', 'Cubo con forma oblicua. Mecánica completamente diferente.', 15.99, 12.99, 25, 1, 'https://placehold.co/300x300/e76f51/ffffff?text=Skewb'),
(5, 'Megaminx', 'Cubo de 12 lados. Para los verdaderos coleccionistas.', 34.99, 27.99, 12, 1, 'https://placehold.co/300x300/6a0572/ffffff?text=Megaminx'),
-- Accesorios
(6, 'Lubricante Cubicle Silk', 'Lubricante premium para cubos. Mejora la fluidez y velocidad.', 9.99, NULL, 100, 1, 'https://placehold.co/300x300/606c38/ffffff?text=Lubricante'),
(6, 'Soporte para Cubo', 'Soporte de acrílico para exhibir tu cubo favorito.', 12.99, 9.99, 50, 1, 'https://placehold.co/300x300/264653/ffffff?text=Soporte'),
(6, 'Bolsa Cubo Viajero', 'Estuche compacto para llevar tu cubo a todas partes.', 8.99, NULL, 60, 1, 'https://placehold.co/300x300/457b9d/ffffff?text=Bolsa+Viajero'),
(6, 'Set de Limpieza', 'Kit completo para limpiar y mantener tus cubos en perfecto estado.', 14.99, 11.99, 35, 1, 'https://placehold.co/300x300/606c38/ffffff?text=Set+Limpieza');