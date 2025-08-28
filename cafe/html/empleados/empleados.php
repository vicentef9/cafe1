<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - Sistema de Cafetería</title>
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
                <h1>Gestión de Empleados</h1>
                <button class="add-button" onclick="mostrarFormulario()">Agregar Empleado</button>
            </header>
            
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Buscar empleado...">
                <select id="filterRole">
                    <option value="">Todos los roles</option>
                    <option value="admin">Administrador</option>
                    <option value="empleado">Empleado</option>
                </select>
            </div>

            <div class="employees-table-container">
                <table class="employees-table">
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
                        <!-- Los empleados se cargarán dinámicamente aquí -->
                    </tbody>
                </table>
            </div>

            <!-- Modal para agregar/editar empleado -->
            <div id="employeeModal" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="cerrarModal()">&times;</span>
                    <h2 id="modalTitle">Agregar Empleado</h2>
                    <form id="employeeForm" onsubmit="guardarEmpleado(event)">
                        <input type="hidden" id="empleadoId">
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
                            <label for="rol">Rol</label>
                            <select id="rol" name="rol" required>
                                <option value="empleado">Empleado</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password">
                            <small id="passwordHelp" class="form-text text-muted">Dejar en blanco para mantener la contraseña actual (solo en edición)</small>
                        </div>
                        <div class="form-group" id="estadoGroup" style="display: none;">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-button">Guardar</button>
                            <button type="button" class="cancel-button" onclick="cerrarModal()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="../../js/empleados.js"></script>
</body>
</html>
