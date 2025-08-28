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
    $stmt = $conn->prepare("SELECT id, nombre, apellido, email, password, rol, estado, fecha_creacion, fecha_actualizacion FROM empleados WHERE id = ?");
    $stmt->execute([$id]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleado) {
        throw new Exception('Empleado no encontrado');
    }

    echo json_encode([
        'success' => true,
        'data' => $empleado
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 