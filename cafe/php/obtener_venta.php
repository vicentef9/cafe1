<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID de venta inválido']);
    exit();
}

try {
    $venta_id = (int)$_GET['id'];
    
    // Obtener información de la venta
    $stmt = $conn->prepare("SELECT v.*, e.nombre as empleado_nombre 
                           FROM ventas v 
                           JOIN empleados e ON v.empleado_id = e.id 
                           WHERE v.id = ?");
    $stmt->execute([$venta_id]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$venta) {
        echo json_encode(['error' => 'Venta no encontrada']);
        exit();
    }
    
    // Obtener detalles de la venta
    $stmt = $conn->prepare("SELECT dv.*, p.nombre as producto_nombre 
                           FROM detalles_venta dv 
                           JOIN productos p ON dv.producto_id = p.id 
                           WHERE dv.venta_id = ?");
    $stmt->execute([$venta_id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'venta' => $venta,
        'detalles' => $detalles
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener la venta: ' . $e->getMessage()]);
}
?>
