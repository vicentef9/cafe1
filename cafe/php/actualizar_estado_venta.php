<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['venta_id']) || !isset($input['nuevo_estado'])) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit();
    }

    $venta_id = $input['venta_id'];
    $nuevo_estado = $input['nuevo_estado'];
    $estado_anterior = $input['estado_anterior'] ?? null;

    // Validar estados permitidos
    $estados_validos = ['pendiente', 'completada', 'cancelada'];
    if (!in_array($nuevo_estado, $estados_validos)) {
        echo json_encode(['success' => false, 'error' => 'Estado no válido']);
        exit();
    }

    $conn->beginTransaction();

    // Obtener detalles de la venta
    $stmt = $conn->prepare("SELECT * FROM detalles_venta WHERE venta_id = ?");
    $stmt->execute([$venta_id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Manejar cambios de inventario según el cambio de estado
    foreach ($detalles as $detalle) {
        $producto_id = $detalle['producto_id'];
        $cantidad = $detalle['cantidad'];

        if ($estado_anterior === 'completada' && $nuevo_estado === 'cancelada') {
            // Devolver stock al inventario
            $stmt = $conn->prepare("UPDATE inventario SET stock_actual = stock_actual + ? WHERE producto_id = ?");
            $stmt->execute([$cantidad, $producto_id]);
        } elseif ($estado_anterior === 'pendiente' && $nuevo_estado === 'completada') {
            // Verificar stock y descontar
            $stmt = $conn->prepare("SELECT stock_actual FROM inventario WHERE producto_id = ?");
            $stmt->execute([$producto_id]);
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stock['stock_actual'] < $cantidad) {
                throw new Exception("Stock insuficiente para completar la venta");
            }
            
            $stmt = $conn->prepare("UPDATE inventario SET stock_actual = stock_actual - ? WHERE producto_id = ?");
            $stmt->execute([$cantidad, $producto_id]);
        } elseif ($estado_anterior === 'completada' && $nuevo_estado === 'pendiente') {
            // Devolver stock al inventario
            $stmt = $conn->prepare("UPDATE inventario SET stock_actual = stock_actual + ? WHERE producto_id = ?");
            $stmt->execute([$cantidad, $producto_id]);
        }
    }

    // Actualizar estado de la venta
    $stmt = $conn->prepare("UPDATE ventas SET estado = ? WHERE id = ?");
    $stmt->execute([$nuevo_estado, $venta_id]);

    $conn->commit();
    echo json_encode(['success' => true, 'mensaje' => 'Estado actualizado correctamente']);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Error al actualizar estado: ' . $e->getMessage()]);
}
?>