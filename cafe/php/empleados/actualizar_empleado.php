<?php
header('Content-Type: application/json');
require_once '../conexion.php';

// Obtener datos del PUT
$datos = json_decode(file_get_contents('php://input'), true);

try {
    // Validar que los datos requeridos estén presentes
    if (!isset($datos['id']) || !isset($datos['nombre']) || !isset($datos['apellido']) || !isset($datos['email']) || !isset($datos['rol'])) {
        throw new Exception('Faltan datos requeridos');
    }

    // Verificar si el email ya existe para otro empleado
    $stmt = $conn->prepare("SELECT id FROM empleados WHERE email = ? AND id != ?");
    $stmt->execute([$datos['email'], $datos['id']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('El email ya está registrado por otro empleado');
    }

    // Preparar la consulta base
    $sql = "UPDATE empleados SET 
            nombre = ?, 
            apellido = ?, 
            email = ?, 
            rol = ?, 
            estado = ?,
            fecha_actualizacion = CURRENT_TIMESTAMP";
    
    $params = [
        $datos['nombre'],
        $datos['apellido'],
        $datos['email'],
        $datos['rol'],
        $datos['estado'] ?? 'activo' // Si no se proporciona estado, mantener activo
    ];

    // Si se proporcionó una nueva contraseña, actualizarla
    if (!empty($datos['password'])) {
        $sql .= ", password = ?";
        $params[] = password_hash($datos['password'], PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $datos['id'];

    // Ejecutar la actualización
    $stmt = $conn->prepare($sql);
    $resultado = $stmt->execute($params);

    if ($resultado) {
        // Obtener el empleado actualizado
        $stmt = $conn->prepare("SELECT id, nombre, apellido, email, rol, estado, fecha_creacion, fecha_actualizacion FROM empleados WHERE id = ?");
        $stmt->execute([$datos['id']]);
        $empleadoActualizado = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'message' => 'Empleado actualizado exitosamente',
            'data' => $empleadoActualizado
        ]);
    } else {
        throw new Exception('Error al actualizar el empleado');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 