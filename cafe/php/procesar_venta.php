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
    
    if (!$input || !isset($input['productos']) || empty($input['productos'])) {
        echo json_encode(['success' => false, 'error' => 'Datos de venta inválidos']);
        exit();
    }

    $conn->beginTransaction();

    // Calcular total
    $total = 0;
    foreach ($input['productos'] as $producto) {
        $total += $producto['subtotal'];
    }

    // Determinar estado basado en disponibilidad de stock
    $estado = 'completada';
    $productosInsuficientes = [];

    // Verificar stock disponible para cada producto
    foreach ($input['productos'] as $producto) {
        $stmt = $conn->prepare("SELECT stock_actual FROM inventario WHERE producto_id = ?");
        $stmt->execute([$producto['id']]);
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$stock || $stock['stock_actual'] < $producto['cantidad']) {
            $productosInsuficientes[] = $producto['nombre'];
            $estado = 'pendiente'; // Cambiar estado si hay productos sin stock suficiente
        }
    }

    // Insertar venta
    $stmt = $conn->prepare("INSERT INTO ventas (empleado_id, total, metodo_pago, estado, fecha_venta) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$_SESSION['usuario_id'], $total, $input['metodo_pago'], $estado]);
    $venta_id = $conn->lastInsertId();

    // Insertar detalles de venta y actualizar inventario
    foreach ($input['productos'] as $producto) {
        // Insertar detalle de venta
        $stmt = $conn->prepare("INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $venta_id,
            $producto['id'],
            $producto['cantidad'],
            $producto['precio'],
            $producto['subtotal']
        ]);

        // Solo actualizar inventario si el estado es completada
        if ($estado === 'completada') {
            $stmt = $conn->prepare("UPDATE inventario SET stock_actual = stock_actual - ? WHERE producto_id = ?");
            $stmt->execute([$producto['cantidad'], $producto['id']]);
        }
    }

    $conn->commit();

    $response = [
        'success' => true, 
        'venta_id' => $venta_id,
        'estado' => $estado
    ];

    if (!empty($productosInsuficientes)) {
        $response['mensaje'] = 'Algunos productos tienen stock insuficiente: ' . implode(', ', $productosInsuficientes) . '. La venta quedó como pendiente.';
    }

    echo json_encode($response);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Error al procesar venta: ' . $e->getMessage()]);
}
?>