
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
-- Contraseña usuario: usuario123

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
INSERT INTO usuarios (nombre, apellidos, email, password, rol, confirmado) 
VALUES ('Test', 'Admin', 'test@tienda.com', 'test12345', 'admin', TRUE),
-- Usuario normal
(
    'María',
    'Rodríguez Martínez',
    'maria@email.com',
    '$2y$12$7tK2pL9mN0oQ3rS4tU5vW6xY7zZ0aB1cD2eF3gH4iJ5kL6mN7oP8q',
    'usuario',
    TRUE
);




