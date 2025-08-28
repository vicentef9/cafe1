<?php
header('Content-Type: application/json');
require_once '../conexion.php';

// Obtener datos del POST
$datos = json_decode(file_get_contents('php://input'), true);

try {
    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM empleados WHERE email = ?");
    $stmt->execute([$datos['email']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('El email ya está registrado');
    }

    // Hash de la contraseña
    $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

    // Insertar nuevo empleado
    $stmt = $conn->prepare("INSERT INTO empleados (nombre, apellido, email, password, rol, estado) VALUES (?, ?, ?, ?, ?, 'activo')");
    $stmt->execute([
        $datos['nombre'],
        $datos['apellido'],
        $datos['email'],
        $password_hash,
        $datos['rol']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Empleado agregado exitosamente'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 