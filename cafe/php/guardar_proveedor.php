<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
require_once '../config/database.php';
session_start();

// Forzar modo de errores de PDO
if (isset($conn) && $conn instanceof PDO) {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} else {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// Validar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Recoger y validar datos
$proveedor_id = (isset($_POST['proveedor_id']) && is_numeric($_POST['proveedor_id']) && intval($_POST['proveedor_id']) > 0) ? intval($_POST['proveedor_id']) : null;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$contacto = isset($_POST['contacto']) ? trim($_POST['contacto']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
$calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 0;
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';

if (!$nombre || !$contacto || !$telefono || !$email || !$categoria || !$direccion) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit;
}

try {
    if (!is_null($proveedor_id)) {
        // Actualizar proveedor existente
        $sql = "UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, email = :email, categoria = :categoria, direccion = :direccion, calificacion = :calificacion, estado = :estado WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $proveedor_id, PDO::PARAM_INT);
    } else {
        // Insertar nuevo proveedor
        $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email, categoria, direccion, calificacion, estado) VALUES (:nombre, :contacto, :telefono, :email, :categoria, :direccion, :calificacion, :estado)";
        $stmt = $conn->prepare($sql);
    }
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':contacto', $contacto);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_INT);
    $stmt->bindParam(':estado', $estado);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Proveedor guardado correctamente']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error al guardar el proveedor: ' . $e->getMessage(), 'sqlstate' => $e->getCode()]);
}
exit;
?>