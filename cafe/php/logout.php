<?php
/**
 * Archivo logout.php
 * 
 * Cierra la sesión del usuario y redirige al inicio
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: ../html/empleados/interfase_administrador.php");
exit();
?>