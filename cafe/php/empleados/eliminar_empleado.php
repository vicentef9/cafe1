<?php
header('Content-Type: application/json');
require_once '../conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'error' => 'ID de empleado no proporcionado'
    ]);
    exit;
}

try {
    // Verificar si el empleado existe
    $stmt = $conn->prepare("SELECT id FROM empleados WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        throw new Exception('Empleado no encontrado');
    }

    // Eliminar el empleado
    $stmt = $conn->prepare("DELETE FROM empleados WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Empleado eliminado exitosamente'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 