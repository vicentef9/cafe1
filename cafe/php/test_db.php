<?php
require_once 'conexion.php';

try {
    // Verificar conexión
    echo "Conexión exitosa<br>";
    
    // Verificar si existe la tabla empleados
    $stmt = $conn->query("SHOW TABLES LIKE 'empleados'");
    if ($stmt->rowCount() > 0) {
        echo "Tabla 'empleados' existe<br>";
        
        // Mostrar estructura de la tabla
        $stmt = $conn->query("DESCRIBE empleados");
        echo "<h3>Estructura de la tabla empleados:</h3>";
        while ($row = $stmt->fetch()) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    } else {
        echo "ERROR: La tabla 'empleados' NO existe<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>