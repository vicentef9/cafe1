-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS cafeteria_db;
USE cafeteria_db;

-- Tabla de empleados (basada en tu imagen y asumiendo estructura similar a 'usuarios')
CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100), -- Agregado según imagen
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado') NOT NULL DEFAULT 'empleado',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo', -- Agregado según imagen
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Agregado según imagen
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
    usuario_id INT NOT NULL, -- Ahora se referirá al ID en la tabla 'empleados'
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') NOT NULL,
    estado ENUM('completada', 'cancelada', 'pendiente') DEFAULT 'completada',
    notas TEXT,
    FOREIGN KEY (usuario_id) REFERENCES empleados(id) -- Referencia a la tabla 'empleados'
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
    FOREIGN KEY (producto_id) REFERENCES productos(id) -- Asumiendo que productos existe
);

-- Índices para mejorar el rendimiento
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);
CREATE INDEX idx_detalles_venta_venta ON detalles_venta(venta_id);
CREATE INDEX idx_detalles_venta_producto ON detalles_venta(producto_id);

-- Eliminar datos existentes para evitar duplicados
DELETE FROM inventario;
DELETE FROM productos;
DELETE FROM empleados;
DELETE FROM ventas;
DELETE FROM detalles_venta;

-- Insertar algunos datos de ejemplo
INSERT INTO empleados (nombre, email, password, rol) VALUES
('Admin', 'admin@cafeteria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO productos (nombre, categoria, descripcion) VALUES
('Café Americano', 'cafe', 'Café negro tradicional'),
('Café Latte', 'cafe', 'Café con leche y espuma'),
('Croissant', 'postres', 'Panadería francesa'),
('Tarta de Manzana', 'postres', 'Postre tradicional'),
('Agua Mineral', 'bebidas', 'Agua mineral natural'),
('Jugo de Naranja', 'bebidas', 'Jugo natural de naranja'),
('Azúcar', 'insumos', 'Azúcar refinada'),
('Leche', 'insumos', 'Leche entera');

INSERT INTO inventario (producto_id, stock_actual, stock_minimo, precio_base, descuento) VALUES
(1, 50, 20, 2.50, 0),
(2, 45, 15, 3.00, 10),
(3, 30, 10, 1.80, 0),
(4, 20, 5, 4.50, 15),
(5, 100, 30, 1.00, 0),
(6, 60, 20, 2.00, 5),
(7, 200, 50, 0.50, 0),
(8, 150, 40, 1.20, 0);

INSERT INTO ventas (usuario_id, total, metodo_pago) VALUES
(1, 10.00, 'efectivo'),
(1, 15.00, 'tarjeta'),
(1, 20.00, 'transferencia');

INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 2, 2.50, 5.00),
(1, 2, 1, 3.00, 3.00),
(2, 1, 1, 2.50, 2.50),
(2, 2, 1, 3.00, 3.00),
(3, 1, 1, 2.50, 2.50),
(3, 2, 1, 3.00, 3.00); 