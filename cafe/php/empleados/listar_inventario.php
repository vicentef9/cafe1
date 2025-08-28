<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $stmt = $conn->prepare("SELECT i.*, p.nombre as producto_nombre, p.categoria 
                            FROM inventario i 
                            JOIN productos p ON i.producto_id = p.id");
    $stmt->execute();
    $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($inventario);
} catch (PDOException $e) {
    echo json_encode([]);
}
exit;
