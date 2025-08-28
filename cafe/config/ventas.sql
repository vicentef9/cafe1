-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS cafeteria_db;
USE cafeteria_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado') NOT NULL DEFAULT 'empleado',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria ENUM('cafe', 'postres', 'bebidas', 'insumos') NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de inventario
CREATE TABLE IF NOT EXISTS inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    stock_actual INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 0,
    precio_base DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(5,2) DEFAULT 0,
    notas TEXT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de ventas
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') NOT NULL,
    estado ENUM('completada', 'cancelada', 'pendiente') DEFAULT 'completada',
    notas TEXT,
    FOREIGN KEY (usuario_id) REFERENCES empleados(id)
);

-- Tabla de detalles de venta
CREATE TABLE IF NOT EXISTS detalles_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Índices para mejorar el rendimiento
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);
CREATE INDEX idx_detalles_venta_venta ON detalles_venta(venta_id);
CREATE INDEX idx_detalles_venta_producto ON detalles_venta(producto_id);

-- Insertar usuario de prueba
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Admin', 'admin@cafeteria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Empleado', 'empleado@cafeteria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'empleado');

-- Insertar productos de prueba
INSERT INTO productos (nombre, categoria, descripcion) VALUES
('Café Americano', 'cafe', 'Café negro tradicional'),
('Café Latte', 'cafe', 'Café con leche y espuma'),
('Croissant', 'postres', 'Panadería francesa'),
('Tarta de Manzana', 'postres', 'Postre tradicional'),
('Agua Mineral', 'bebidas', 'Agua mineral natural'),
('Jugo de Naranja', 'bebidas', 'Jugo natural de naranja');

-- Insertar inventario de prueba
INSERT INTO inventario (producto_id, stock_actual, stock_minimo, precio_base, descuento) VALUES
(1, 50, 20, 2.50, 0),
(2, 45, 15, 3.00, 10),
(3, 30, 10, 1.80, 0),
(4, 20, 5, 4.50, 15),
(5, 100, 30, 1.00, 0),
(6, 60, 20, 2.00, 5); 