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
if (!isset($_POST['inventario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Faltan datos requeridos']);
    exit();
}

$inventario_id = intval($_POST['inventario_id']);

if ($inventario_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

try {
    $query = "DELETE FROM inventario WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $inventario_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Producto eliminado del inventario correctamente']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No se encontró el producto en el inventario']);
        }
    } else {
        throw new Exception('Error al eliminar el producto del inventario');
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al eliminar el producto: ' . $e->getMessage()]);
}
?>