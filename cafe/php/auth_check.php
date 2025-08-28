<?php
session_start();

function requireLogin() {
    if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
        header("Location: ../html/autenticacion/login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        header("Location: ../html/empleados/interfase_empleado.html");
        exit();
    }
}

function requireEmpleado() {
    requireLogin();
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
        header("Location: ../html/admin/interfase_administrador.html");
        exit();
    }
}
?> 