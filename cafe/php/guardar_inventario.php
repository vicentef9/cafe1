<?php
session_start();
require_once '../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../html/autenticacion/login.php');
    exit();
}

// Validar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método no permitido';
    exit();
}

// Validar y sanitizar datos
$inventario_id = isset($_POST['inventario_id']) ? intval($_POST['inventario_id']) : null;
$producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : null;
$stock_actual = isset($_POST['stock_actual']) ? intval($_POST['stock_actual']) : null;
$stock_minimo = isset($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : null;
$precio_base = isset($_POST['precio_base']) ? floatval($_POST['precio_base']) : null;
$descuento = isset($_POST['descuento']) ? floatval($_POST['descuento']) : 0;
$notas = isset($_POST['notas']) ? trim($_POST['notas']) : '';

// Validaciones básicas
if (!$producto_id || $stock_actual === null || $stock_minimo === null || $precio_base === null) {
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "error" => "Datos incompletos o inválidos."
    ]);
    exit();
}

header('Content-Type: application/json');

try {
    if ($inventario_id) {
        // Actualizar inventario existente
        $sql = "UPDATE inventario SET producto_id = :producto_id, stock_actual = :stock_actual, stock_minimo = :stock_minimo, precio_base = :precio_base, descuento = :descuento, notas = :notas WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $inventario_id, PDO::PARAM_INT);
    } else {
        // Verificar si el producto ya existe en el inventario
        $check_sql = "SELECT id FROM inventario WHERE producto_id = :producto_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            echo json_encode([
                "success" => false,
                "error" => "Este producto ya existe en el inventario. Use la opción de editar para modificarlo."
            ]);
            exit();
        }
        
        // Insertar nuevo producto en inventario
        $sql = "INSERT INTO inventario (producto_id, stock_actual, stock_minimo, precio_base, descuento, notas) VALUES (:producto_id, :stock_actual, :stock_minimo, :precio_base, :descuento, :notas)";
        $stmt = $conn->prepare($sql);
    }
    $stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
    $stmt->bindParam(':stock_actual', $stock_actual, PDO::PARAM_INT);
    $stmt->bindParam(':stock_minimo', $stock_minimo, PDO::PARAM_INT);
    $stmt->bindParam(':precio_base', $precio_base);
    $stmt->bindParam(':descuento', $descuento);
    $stmt->bindParam(':notas', $notas);
    $stmt->execute();
    echo json_encode([
        "success" => true,
        "message" => "Guardado con éxito"
    ]);
    exit();
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => "Error al guardar inventario"
    ]);
    exit();
}
?>