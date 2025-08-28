<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID no proporcionado']);
    exit();
}

$id = $_GET['id'];

try {
    $query = "SELECT * FROM productos WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        header('Content-Type: application/json');
        echo json_encode($producto);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Producto no encontrado']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al obtener el producto']);
}
?> 