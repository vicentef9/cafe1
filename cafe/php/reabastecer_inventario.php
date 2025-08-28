<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Verificar si se recibieron los datos necesarios
if (!isset($_POST['inventario_id']) || !isset($_POST['cantidad'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Faltan datos requeridos']);
    exit();
}

$inventario_id = intval($_POST['inventario_id']);
$cantidad = intval($_POST['cantidad']);

if ($inventario_id <= 0 || $cantidad <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Datos inválidos']);
    exit();
}

try {
    // Actualizar el stock actual sumando la cantidad
    $query = "UPDATE inventario SET stock_actual = stock_actual + :cantidad WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->bindParam(':id', $inventario_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Stock reabastecido correctamente']);
    } else {
        throw new Exception('Error al reabastecer el producto');
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al reabastecer el producto: ' . $e->getMessage()]);
}
?>
