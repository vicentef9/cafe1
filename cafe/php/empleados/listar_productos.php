<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $stmt = $conn->prepare("SELECT id, nombre, categoria FROM productos ORDER BY nombre");
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($productos);
} catch (PDOException $e) {
    echo json_encode([]);
}
exit;
