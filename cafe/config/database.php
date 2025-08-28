<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'cafeteria_db';
$username = 'root';
$password = '';

try {
    // Crear conexión PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar el modo de error de PDO para que lance excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar el modo de obtención de resultados por defecto
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // En caso de error, mostrar mensaje y terminar la ejecución
    die("Error de conexión: " . $e->getMessage());
}
?> 