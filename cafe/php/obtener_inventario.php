<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID no proporcionado']);
    exit();
}

$id = $_GET['id'];

try {
    // Obtener los datos del inventario
    $query = "SELECT i.*, p.nombre as producto_nombre 
              FROM inventario i 
              JOIN productos p ON i.producto_id = p.id 
              WHERE i.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Producto no encontrado']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?> 