<?php
session_start();
require_once '../../config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../autenticacion/login.php');
    exit();
}

// Obtener el rol del usuario

$rol = $_SESSION['rol'];

// Verificar si el usuario tiene permisos para acceder a esta página
if ($rol !== 'empleado' && $rol !== 'administrador') {
    header('Location: ../autenticacion/login.php');
    exit();
}

// Obtener datos de proveedores
$query = "SELECT * FROM proveedores ORDER BY id ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores - Sistema de Cafetería</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/styles-proveedores.css?v=1.2">
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
            <div class="suppliers-header">
                <h1>Gestión de Proveedores</h1>
                <button class="add-supplier-btn" onclick="mostrarFormulario()">Agregar Proveedor</button>
            </div>
            
            <div class="search-filters">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Buscar proveedor...">
                    <button class="search-button" onclick="filtrarProveedores()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
                
                <div class="filters-container">
                    <div class="filter-group">
                        <label for="filterCategory">Categoría de Producto</label>
                        <select id="filterCategory">
                            <option value="">Todas las categorías</option>
                            <option value="cafe">Café</option>
                            <option value="postres">Postres</option>
                            <option value="bebidas">Bebidas</option>
                            <option value="insumos">Insumos</option>
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
                    
                    <div class="filter-group">
                        <label for="filterRating">Calificación</label>
                        <select id="filterRating">
                            <option value="">Todas las calificaciones</option>
                            <option value="5">5 estrellas</option>
                            <option value="4">4+ estrellas</option>
                            <option value="3">3+ estrellas</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="suppliers-table-container">
                <table class="suppliers-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Categoría</th>
                            <th>Calificación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($result) > 0) {
                            foreach($result as $row) {
                                $estado_class = $row['estado'] === 'activo' ? 'active' : 'inactive';
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['contacto']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                            <td>
                                <div class="rating">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        $class = $i <= $row['calificacion'] ? 'filled' : '';
                                        echo "<span class='star $class'>★</span>";
                                    }
                                    ?>
                                </div>
                            </td>
                            <td><span class="status <?php echo $estado_class; ?>"><?php echo ucfirst($row['estado']); ?></span></td>
                            <td>
                                <button class="action-button edit" onclick="editarProveedor(<?php echo $row['id']; ?>)">Editar</button>
                                <button class="action-button delete" onclick="eliminarProveedor(<?php echo $row['id']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="9" class="no-data">No hay proveedores registrados</td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal para agregar/editar proveedor -->
            <div id="supplierModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 id="modalTitle">Agregar Proveedor</h2>
                        <span class="close-button" onclick="cerrarModal()">&times;</span>
                    </div>
                    <form id="supplierForm" class="form-grid-2col" action="../../php/guardar_proveedor.php" method="POST" onsubmit="return validarFormularioProveedor()">
                        <input type="hidden" id="proveedor_id" name="proveedor_id">
                        <div class="form-group">
                            <label for="nombre">Nombre de la Empresa *</label>
                            <input type="text" id="nombre" name="nombre" required maxlength="100" 
                                   placeholder="Ej: Distribuidora San Pedro">
                            <small class="form-hint">Solo letras y espacios, sin números ni caracteres especiales</small>
                        </div>
                        <div class="form-group">
                            <label for="contacto">Nombre del Contacto *</label>
                            <input type="text" id="contacto" name="contacto" required maxlength="100"
                                   placeholder="Ej: María González">
                            <small class="form-hint">Solo letras y espacios, sin números ni caracteres especiales</small>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono *</label>
                            <input type="tel" id="telefono" name="telefono" required maxlength="20"
                                   placeholder="Ej: +56 9 1234 5678">
                            <small class="form-hint">Mínimo 8 dígitos</small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required maxlength="100"
                                   placeholder="Ej: contacto@empresa.com">
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría de Producto *</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría...</option>
                                <option value="cafe">Café</option>
                                <option value="postres">Postres</option>
                                <option value="bebidas">Bebidas</option>
                                <option value="insumos">Insumos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección *</label>
                            <textarea id="direccion" name="direccion" rows="3" required maxlength="255"
                                      placeholder="Dirección completa del proveedor"></textarea>
                            <small class="form-hint">Mínimo 5 caracteres</small>
                        </div>
                        <div class="form-group">
                            <label for="calificacion">Calificación</label>
                            <select id="calificacion" name="calificacion">
                                <option value="0">Sin calificar</option>
                                <option value="1">1 estrella</option>
                                <option value="2">2 estrellas</option>
                                <option value="3">3 estrellas</option>
                                <option value="4">4 estrellas</option>
                                <option value="5">5 estrellas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado *</label>
                            <select id="estado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-button">Guardar Proveedor</button>
                            <button type="button" class="cancel-button" onclick="cerrarModal()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
    // Validación completa del formulario de proveedores
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('supplierForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validarFormularioProveedor()) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            });
        }
    });

    function validarFormularioProveedor() {
        // Limpiar errores previos
        limpiarErroresCampos();
        
        // Obtener valores de los campos
        var nombre = document.getElementById('nombre').value.trim();
        var contacto = document.getElementById('contacto').value.trim();
        var telefono = document.getElementById('telefono').value.trim();
        var email = document.getElementById('email').value.trim();
        var categoria = document.getElementById('categoria').value;
        var direccion = document.getElementById('direccion').value.trim();
        var estado = document.getElementById('estado').value;
        
        var errores = [];
        var camposConError = [];
        
        // Validación de nombre (solo letras y espacios, SIN números)
        var soloLetrasEspacios = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
        var contieneNumeros = /\d/;
        var contieneCaracteresEspeciales = /[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/;
        
        if (!nombre) {
            errores.push('El nombre de la empresa es obligatorio.');
            camposConError.push('nombre');
        } else if (nombre.length < 2) {
            errores.push('El nombre debe tener al menos 2 caracteres.');
            camposConError.push('nombre');
        } else if (contieneNumeros.test(nombre)) {
            errores.push('El nombre de la empresa no puede contener números.');
            camposConError.push('nombre');
        } else if (contieneCaracteresEspeciales.test(nombre)) {
            errores.push('El nombre de la empresa no puede contener caracteres especiales (símbolos, puntuación, etc.).');
            camposConError.push('nombre');
        } else if (!soloLetrasEspacios.test(nombre)) {
            errores.push('El nombre de la empresa solo puede contener letras y espacios.');
            camposConError.push('nombre');
        }
        
        // Validación de contacto (solo letras y espacios, SIN números)
        var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
        if (!contacto) {
            errores.push('El nombre del contacto es obligatorio.');
            camposConError.push('contacto');
        } else if (contacto.length < 2) {
            errores.push('El nombre del contacto debe tener al menos 2 caracteres.');
            camposConError.push('contacto');
        } else if (contieneNumeros.test(contacto)) {
            errores.push('El nombre del contacto no puede contener números.');
            camposConError.push('contacto');
        } else if (contieneCaracteresEspeciales.test(contacto)) {
            errores.push('El nombre del contacto no puede contener caracteres especiales (símbolos, puntuación, etc.).');
            camposConError.push('contacto');
        } else if (!soloLetras.test(contacto)) {
            errores.push('El nombre del contacto solo puede contener letras y espacios.');
            camposConError.push('contacto');
        }
        
        // Validación de teléfono
        var telefonoRegex = /^[+]?[\d\s-()]+$/;
        if (!telefono) {
            errores.push('El teléfono es obligatorio.');
            camposConError.push('telefono');
        } else if (!telefonoRegex.test(telefono)) {
            errores.push('El teléfono solo puede contener números, espacios, guiones, paréntesis y el símbolo +');
            camposConError.push('telefono');
        } else if (telefono.replace(/[\s-()]/g, '').length < 8) {
            errores.push('El teléfono debe tener al menos 8 dígitos.');
            camposConError.push('telefono');
        }
        
        // Validación de email
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            errores.push('El email es obligatorio.');
            camposConError.push('email');
        } else if (!emailRegex.test(email)) {
            errores.push('Por favor ingrese un email válido.');
            camposConError.push('email');
        }
        
        // Validación de categoría
        if (!categoria) {
            errores.push('Debe seleccionar una categoría.');
            camposConError.push('categoria');
        }
        
        // Validación de dirección
        if (!direccion) {
            errores.push('La dirección es obligatoria.');
            camposConError.push('direccion');
        } else if (direccion.length < 5) {
            errores.push('La dirección debe tener al menos 5 caracteres.');
            camposConError.push('direccion');
        }
        
        // Validación de estado
        if (!estado) {
            errores.push('Debe seleccionar un estado.');
            camposConError.push('estado');
        }
        
        // Marcar campos con errores
        camposConError.forEach(function(campo) {
            marcarCampoConError(campo);
        });
        
        // Mostrar errores si existen
        if (errores.length > 0) {
            var mensajeError = '❌ SE ENCONTRARON LOS SIGUIENTES ERRORES:\n\n';
            errores.forEach(function(error, index) {
                mensajeError += '• ' + error + '\n';
            });
            mensajeError += '\n⚠️ Por favor corrija los campos marcados en rojo antes de continuar.';
            alert(mensajeError);
            
            // Hacer scroll al primer campo con error
            if (camposConError.length > 0) {
                var primerCampoError = document.getElementById(camposConError[0]);
                if (primerCampoError) {
                    primerCampoError.focus();
                    primerCampoError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            
            return false;
        }
        
        // Si todo está correcto, mostrar confirmación
        var confirmacion = confirm('¿Está seguro de que desea guardar este proveedor con la información ingresada?');
        if (!confirmacion) {
            return false;
        }
        
        return true;
    }

    function limpiarErroresCampos() {
        // Remover clase de error de todos los campos
        var campos = ['nombre', 'contacto', 'telefono', 'email', 'categoria', 'direccion', 'estado'];
        campos.forEach(function(campo) {
            var elemento = document.getElementById(campo);
            if (elemento) {
                elemento.classList.remove('error');
            }
        });
    }

    function marcarCampoConError(nombreCampo) {
        var campo = document.getElementById(nombreCampo);
        if (campo) {
            campo.classList.add('error');
        }
    }

    function validarCampoEnTiempoReal(nombreCampo) {
        var campo = document.getElementById(nombreCampo);
        if (campo) {
            campo.addEventListener('input', function() {
                campo.classList.remove('error');
            });
        }
    }

    // Agregar validación en tiempo real cuando se carga la página
    document.addEventListener('DOMContentLoaded', function() {
        var campos = ['nombre', 'contacto', 'telefono', 'email', 'categoria', 'direccion', 'estado'];
        campos.forEach(validarCampoEnTiempoReal);
        
        // Validación especial para campos de nombre (solo letras)
        validarSoloLetras('nombre');
        validarSoloLetras('contacto');
    });

    function validarSoloLetras(nombreCampo) {
        var campo = document.getElementById(nombreCampo);
        if (campo) {
            campo.addEventListener('input', function(e) {
                var valor = e.target.value;
                var valorLimpio = valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
                
                if (valor !== valorLimpio) {
                    e.target.value = valorLimpio;
                    // Mostrar mensaje temporal
                    mostrarMensajeTemporal(nombreCampo, 'Solo se permiten letras y espacios');
                }
                
                // Limpiar clase de error
                campo.classList.remove('error');
            });
            
            campo.addEventListener('keypress', function(e) {
                var charCode = e.which || e.keyCode;
                var char = String.fromCharCode(charCode);
                
                // Permitir teclas especiales (backspace, delete, enter, etc.)
                if (charCode <= 32) return true;
                
                // Solo permitir letras y espacios
                var soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]$/;
                if (!soloLetras.test(char)) {
                    e.preventDefault();
                    
                    // Efecto visual de prevención
                    campo.classList.add('prevented-input');
                    setTimeout(function() {
                        campo.classList.remove('prevented-input');
                    }, 300);
                    
                    mostrarMensajeTemporal(nombreCampo, 'Solo se permiten letras y espacios');
                    return false;
                }
            });
        }
    }

    function mostrarMensajeTemporal(nombreCampo, mensaje) {
        var campo = document.getElementById(nombreCampo);
        if (campo) {
            // Remover mensaje anterior si existe
            var mensajeAnterior = campo.parentNode.querySelector('.mensaje-temporal');
            if (mensajeAnterior) {
                mensajeAnterior.remove();
            }
            
            // Crear nuevo mensaje
            var mensajeDiv = document.createElement('div');
            mensajeDiv.className = 'mensaje-temporal';
            mensajeDiv.textContent = mensaje;
            mensajeDiv.style.cssText = 'color: #dc3545; font-size: 0.8em; margin-top: 2px; font-style: italic;';
            
            // Insertar después del campo
            campo.parentNode.insertBefore(mensajeDiv, campo.nextSibling);
            
            // Eliminar mensaje después de 3 segundos
            setTimeout(function() {
                if (mensajeDiv.parentNode) {
                    mensajeDiv.remove();
                }
            }, 3000);
        }
    }
    </script>
    <script src="../../js/proveedores.js"></script>
</body>
</html>