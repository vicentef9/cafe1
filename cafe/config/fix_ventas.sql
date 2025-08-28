USE cafeteria_db;

-- Eliminar la tabla ventas si existe
DROP TABLE IF EXISTS detalles_venta;
DROP TABLE IF EXISTS ventas;

-- Recrear la tabla ventas con la estructura correcta
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') NOT NULL,
    estado ENUM('completada', 'cancelada', 'pendiente') DEFAULT 'completada',
    notas TEXT,
    FOREIGN KEY (usuario_id) REFERENCES empleados(id)
);

-- Recrear la tabla detalles_venta
CREATE TABLE detalles_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Recrear los índices
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);
CREATE INDEX idx_detalles_venta_venta ON detalles_venta(venta_id);
CREATE INDEX idx_detalles_venta_producto ON detalles_venta(producto_id); 