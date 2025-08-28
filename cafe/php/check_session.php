<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar encabezados para JSON
header('Content-Type: application/json');

// Verificar si el usuario tiene sesión iniciada
$response = [
    'loggedIn' => isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']),
    'username' => isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : '',
    'rol' => isset($_SESSION['rol']) ? $_SESSION['rol'] : ''
];

// Devolver respuesta en formato JSON
echo json_encode($response);
?>