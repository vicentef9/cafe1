<?php
session_start();

// Verificar si el usuario está logueado (opcional para esta página)
// if (!isset($_SESSION['usuario_id'])) {
//     header('Location: ../autenticacion/login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Sistema de Cafetería</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/styles-usuarios.css">
</head>
<body>
      <div class="dashboard-container">
        <div class="sidebar">
            <div class="logo">
                <h2>Sistema de <br> Cafetería</h2>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="interfase_administrador.php" class="nav-item active">Inicio</a></li>
                    <li><a href="catalogo.php" class="nav-item">Catálogo</a></li>
                    <li><a href="admin_usuarios.php" class="nav-item active">Usuarios</a></li>
                    <li><a href="productos.php" class="nav-item">Productos</a></li>
                    <li><a href="inventario.php" class="nav-item">Inventario</a></li>
                    <li><a href="proveedores.php" class="nav-item">Proveedores</a></li>
                    <li><a href="ventas.php" class="nav-item">Ventas</a></li>
                    <li><a href="soporte.php" class="nav-item">Soporte</a></li>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li><a href="../../php/logout.php" class="nav-item logout-btn">Cerrar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        
        <main class="main-content">
            <header class="dashboard-header">
                <h1>Panel de Administración</h1>
            </header>
            
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>Gestión de Empleados</h3>
                    <p>Administra el personal de la cafetería</p>
                    <a href="admin_usuarios.php" class="card-link">Acceder</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>Productos</h3>
                    <p>Gestiona el catálogo de productos</p>
                    <a href="productos.php" class="card-link">Acceder</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>Inventario</h3>
                    <p>Control de existencias y descuentos</p>
                    <a href="inventario.php" class="card-link">Acceder</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>Proveedores</h3>
                    <p>Gestiona los proveedores de productos</p>
                    <a href="proveedores.php" class="card-link">Acceder</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>Ventas</h3>
                    <p>Registro y control de ventas</p>
                    <a href="ventas.php" class="card-link">Acceder</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>Soporte</h3>
                    <p>Preguntas frecuentes y ayuda</p>
                    <a href="soporte.php" class="card-link">Acceder</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
