<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

// Verificar si es una petición POST y si se proporcionó un ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Actualizar el producto para desactivarlo
        $query = "UPDATE productos SET activo = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Producto desactivado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
} else {
    // Respuesta de depuración para ver qué llega en $_POST
    echo json_encode([
        'success' => false,
        'error' => 'Petición inválida o falta el ID.',
        'debug_post' => $_POST
    ]);
    exit();
}
?>