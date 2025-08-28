<?php
session_start();

// Corregir la ruta de conexion.php
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../autenticacion/login.php');
    exit();
}

// Obtener el rol del usuario
$rol = $_SESSION['rol'];

// Verificar si el usuario tiene permisos para acceder a esta página
if ($rol !== 'administrador') {
    header('Location: ../autenticacion/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Cafetería</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/styles-usuarios.css?v=3.0">
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
            <div class="users-header">
                <h1>Gestión de Usuarios</h1>
                <button class="add-user-btn" onclick="mostrarFormulario()">Agregar Usuario</button>
            </div>
            
            <div class="search-filters">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Buscar usuario...">
                    <button type="button" class="search-button" onclick="filtrarUsuarios()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
                
                <div class="filters-container">
                    <div class="filter-group">
                        <label for="filterRole">Rol</label>
                        <select id="filterRole">
                            <option value="">Todos los roles</option>
                            <option value="administrador">Administrador</option>
                            <option value="empleado">Empleado</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filterStatus">Estado</label>
                        <select id="filterStatus">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="users-table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="empleadosTableBody">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- Modal para agregar/editar usuario -->
            <div id="userModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 id="modalTitle">Agregar Usuario</h2>
                        <span class="close-button" onclick="cerrarModal()">&times;</span>
                    </div>
                    <form id="userForm" class="form-grid-2col">
                        <input type="hidden" id="usuario_id" name="id">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="rol">Rol</label>
                            <select id="rol" name="rol" required>
                                <option value="administrador">Administrador</option>
                                <option value="empleado">Empleado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-button">Guardar Usuario</button>
                            <button type="button" class="cancel-button" onclick="cerrarModal()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="../../js/admin_usuarios.js?v=2.0"></script>
</body>
</html>