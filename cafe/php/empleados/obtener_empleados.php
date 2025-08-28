<?php
header('Content-Type: application/json');
require_once '../conexion.php';

try {
    // Verificar la conexión
    if (!$conn) {
        throw new Exception('No hay conexión a la base de datos');
    }

    // Verificar si la tabla existe
    $stmt = $conn->query("SHOW TABLES LIKE 'empleados'");
    if ($stmt->rowCount() === 0) {
        throw new Exception('La tabla empleados no existe');
    }

    // Obtener los empleados
    $stmt = $conn->prepare("SELECT id, nombre, apellido, email, rol, estado, fecha_creacion, fecha_actualizacion FROM empleados ORDER BY id DESC");
    $stmt->execute();
    $empleados = $stmt->fetchAll();

    // Verificar si hay empleados
    if (empty($empleados)) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No hay empleados registrados'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $empleados
    ]);
} catch (Exception $e) {
    error_log('Error en obtener_empleados.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener empleados: ' . $e->getMessage()
    ]);
}
?> 