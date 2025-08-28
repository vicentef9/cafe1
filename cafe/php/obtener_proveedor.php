<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        
        $query = "SELECT * FROM proveedores WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($proveedor) {
            echo json_encode(['success' => true, 'proveedor' => $proveedor]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Solicitud inválida']);
}