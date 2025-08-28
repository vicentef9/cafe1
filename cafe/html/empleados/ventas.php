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
if ($rol !== 'empleado' && $rol !== 'admin') {
    header('Location: ../autenticacion/login.php');
    exit();
}

// Obtener productos disponibles
$query_productos = "SELECT p.*, i.precio_base, i.stock_actual \n                   FROM productos p \n                   JOIN inventario i ON p.id = i.producto_id \n                   WHERE i.stock_actual > 0";
$stmt_productos = $conn->prepare($query_productos);
$stmt_productos->execute();
$productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas - Sistema de Cafetería</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/styles-ventas.css?v=2.0">
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
            <div class="sales-header">
                <h1>Gestión de Ventas</h1>
                <button class="add-sale-btn" onclick="mostrarFormulario()">Nueva Venta</button>
            </div>

            <section class="sales-summary">
                <div class="summary-card">
                    <h3>Ventas del Día</h3>
                    <p class="amount" id="ventasDia">CLP 0</p>
                    <p class="sub-info" id="totalVentasDia">0 ventas</p>
                </div>
                <div class="summary-card">
                    <h3>Ventas del Mes</h3>
                    <p class="amount" id="ventasMes">CLP 0</p>
                    <p class="sub-info" id="totalVentasMes">0 ventas</p>
                </div>
                <div class="summary-card">
                    <h3>Productos Vendidos</h3>
                    <p class="amount" id="productosVendidos">0</p>
                    <p class="sub-info" id="empleadosActivos">0 empleados</p>
                </div>
                <div class="summary-card">
                    <h3>Ticket Promedio</h3>
                    <p class="amount" id="ticketPromedio">CLP 0</p>
                </div>
                <div class="summary-card">
                    <h3>Horario de Operación</h3>
                    <p class="sub-info" id="primeraVenta">Primera venta: --:--</p>
                    <p class="sub-info" id="ultimaVenta">Última venta: --:--</p>
                </div>
            </section>

            <section class="search-filters">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Buscar venta...">
                    <button class="search-button" onclick="filtrarVentas()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <div class="filters-container">
                    <div class="filter-group">
                        <label for="filterDate">Fecha</label>
                        <input type="date" id="filterDate">
                    </div>

                    <div class="filter-group">
                        <label for="filterPayment">Método de Pago</label>
                        <select id="filterPayment">
                            <option value="">Todos</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterStatus">Estado</label>
                        <select id="filterStatus">
                            <option value="">Todos</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="sales-table-container">
                <h2>Registro de Ventas</h2>
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Método de Pago</th>
                            <th>Estado</th>
                            <th>Empleado</th>
                            <th>Notas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ventasTableBody">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </section>

            <section class="productos-populares">
                <h2>Productos Más Vendidos</h2>
                <ol id="productosPopulares">
                    <!-- Los datos se cargarán dinámicamente -->
                </ol>
            </section>

            <!-- Modal para nueva venta -->
            <div id="saleModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Nueva Venta</h2>
                        <span class="close-button" onclick="cerrarModal()">&times;</span>
                    </div>
                    <form id="saleForm" class="sales-form" onsubmit="procesarVenta(event)">
                        <div class="form-group">
                            <label for="producto">Producto</label>
                            <select id="producto" name="producto" required onchange="actualizarPrecio()">
                                <option value="">Seleccionar producto...</option>
                                <?php foreach ($productos as $producto): ?>
                                <option value="<?php echo $producto['id']; ?>" 
                                        data-precio="<?php echo $producto['precio_base']; ?>"
                                        data-stock="<?php echo $producto['stock_actual']; ?>">
                                    <?php echo htmlspecialchars($producto['nombre']); ?> - CLP<?php echo number_format($producto['precio_base'], 0, ',', '.'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cantidad">Cantidad</label>
                            <input type="number" id="cantidad" name="cantidad" min="1" value="1" required>
                        </div>
                        <div class="form-group">
                            <label for="metodoPago">Método de Pago</label>
                            <select id="metodoPago" name="metodoPago" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div class="cart-summary">
                            <h3>Resumen de la Venta</h3>
                            <div id="cartItems">
                                <!-- Los items se agregarán dinámicamente -->
                            </div>
                            <div class="cart-total">
                                <span>Total:</span>
                                <span id="totalAmount">CLP 0</span>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="add-to-cart-button" onclick="agregarAlCarrito()">Agregar al Carrito</button>
                            <button type="submit" class="submit-button">Completar Venta</button>
                            <button type="button" class="cancel-button" onclick="cerrarModal()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal para ver detalles de venta -->
            <div id="detailsModal" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="cerrarModal()">&times;</span>
                    <h2>Detalles de la Venta</h2>
                    <div class="sale-details">
                        <div class="detail-group">
                            <label>ID Venta:</label>
                            <span id="detailId"></span>
                        </div>
                        <div class="detail-group">
                            <label>Fecha:</label>
                            <span id="detailDate"></span>
                        </div>
                        <div class="detail-group">
                            <label>Empleado:</label>
                            <span id="detailEmployee"></span>
                        </div>
                        <div class="detail-group">
                            <label>Método de Pago:</label>
                            <span id="detailPayment"></span>
                        </div>
                        <div class="detail-items">
                            <h3>Productos</h3>
                            <table class="details-table">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                                <tbody id="detailItems">
                                    <!-- Los items se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <div class="detail-total">
                            <span>Total:</span>
                            <span id="detailTotal"></span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Variables globales
        let carrito = [];

        // Funciones para el modal de nueva venta
        function mostrarFormulario() {
            document.getElementById('saleModal').style.display = 'block';
            carrito = [];
            actualizarCarrito();
        }

        function cerrarModal() {
            document.getElementById('saleModal').style.display = 'none';
            document.getElementById('detailsModal').style.display = 'none';
        }

        function actualizarPrecio() {
            const producto = document.getElementById('producto');
            const cantidad = document.getElementById('cantidad');
            if (producto.value) {
                const precio = parseFloat(producto.options[producto.selectedIndex].dataset.precio);
                const stock = parseInt(producto.options[producto.selectedIndex].dataset.stock);
                cantidad.max = stock;
            }
        }

        function agregarAlCarrito() {
            const producto = document.getElementById('producto');
            const cantidad = document.getElementById('cantidad');
            
            if (!producto.value) {
                alert('Por favor seleccione un producto');
                return;
            }
            
            if (!cantidad.value || cantidad.value <= 0) {
                alert('Por favor ingrese una cantidad válida');
                return;
            }
            
            const nombre = producto.options[producto.selectedIndex].text.split(' - ')[0];
            const precio = parseFloat(producto.options[producto.selectedIndex].dataset.precio);
            const cantidadIngresada = parseInt(cantidad.value);
            const stock = parseInt(producto.options[producto.selectedIndex].dataset.stock);
            const id = producto.value;
            
            // Verificar si hay suficiente stock
            if (cantidadIngresada > stock) {
                alert(`Stock insuficiente. Solo hay ${stock} unidades disponibles.`);
                return;
            }
            
            // Verificar si el producto ya está en el carrito
            const productoExistente = carrito.find(item => item.id === id);
            if (productoExistente) {
                // Si ya existe, sumar la cantidad
                const nuevaCantidad = productoExistente.cantidad + cantidadIngresada;
                if (nuevaCantidad > stock) {
                    alert(`No se puede agregar. Total excedería el stock disponible (${stock} unidades).`);
                    return;
                }
                productoExistente.cantidad = nuevaCantidad;
                productoExistente.subtotal = productoExistente.precio * nuevaCantidad;
            } else {
                // Si no existe, agregarlo al carrito
                carrito.push({
                    id,
                    nombre,
                    cantidad: cantidadIngresada,
                    precio,
                    subtotal: precio * cantidadIngresada
                });
            }
            
            actualizarCarrito();
            
            // Limpiar el formulario
            producto.value = '';
            cantidad.value = 1;
            
            alert('Producto agregado al carrito correctamente');
        }

        function actualizarCarrito() {
            const cartItems = document.getElementById('cartItems');
            cartItems.innerHTML = '';
            let total = 0;

            if (carrito.length === 0) {
                cartItems.innerHTML = '<p class="empty-cart">El carrito está vacío</p>';
            } else {
                carrito.forEach((item, index) => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'cart-item';
                    itemElement.innerHTML = `
                        <div class="item-info">
                            <span class="item-name">${item.nombre}</span>
                            <span class="item-details">x${item.cantidad} - CLP ${item.precio.toFixed(0)} c/u</span>
                        </div>
                        <div class="item-actions">
                            <span class="item-subtotal">CLP ${item.subtotal.toFixed(0)}</span>
                            <button class="remove-item" onclick="eliminarDelCarrito(${index})" title="Eliminar">×</button>
                        </div>
                    `;
                    cartItems.appendChild(itemElement);
                    total += item.subtotal;
                });
            }

            document.getElementById('totalAmount').textContent = `CLP ${total.toFixed(0)}`;
        }

        function eliminarDelCarrito(index) {
            carrito.splice(index, 1);
            actualizarCarrito();
        }

        function actualizarTablaVentas(ventas) {
            console.log('Datos recibidos en actualizarTablaVentas:', ventas);
            const tbody = document.getElementById('ventasTableBody');
            tbody.innerHTML = '';
            
            ventas.forEach(venta => {
                const row = document.createElement('tr');
                const totalVenta = parseFloat(venta.total || 0).toFixed(0);
                
                // Definir clase CSS para el estado
                const estadoClass = getEstadoClass(venta.estado);
                
                row.innerHTML = `
                    <td>#${venta.id.toString().padStart(3, '0')}</td>
                    <td>${venta.fecha_venta}</td>
                    <td>
                        <button class="view-details" onclick="mostrarDetalles(${venta.id})">Ver Detalles</button>
                    </td>
                    <td>CLP ${totalVenta}</td>
                    <td>${venta.metodo_pago}</td>
                    <td>
                        <select class="status-select ${estadoClass}" onchange="cambiarEstado(${venta.id}, this.value, '${venta.estado}')">
                            <option value="pendiente" ${venta.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                            <option value="completada" ${venta.estado === 'completada' ? 'selected' : ''}>Completada</option>
                            <option value="cancelada" ${venta.estado === 'cancelada' ? 'selected' : ''}>Cancelada</option>
                        </select>
                    </td>
                    <td>${venta.empleado_nombre}</td>
                    <td>${venta.notas || 'N/A'}</td>
                    <td>
                        <button class="action-button view" onclick="mostrarDetalles(${venta.id})">Ver</button>
                        <button class="action-button print" onclick="imprimirTicket(${venta.id})">Imprimir</button>
                        ${venta.estado === 'cancelada' ? '' : `<button class="action-button cancel" onclick="cancelarVenta(${venta.id})">Cancelar</button>`}
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function getEstadoClass(estado) {
            switch(estado) {
                case 'completada': return 'completada';
                case 'pendiente': return 'pendiente';
                case 'cancelada': return 'cancelada';
                default: return '';
            }
        }

        function cambiarEstado(ventaId, nuevoEstado, estadoAnterior) {
            if (nuevoEstado === estadoAnterior) return;
            
            const confirmacion = confirm(`¿Está seguro de cambiar el estado de la venta #${ventaId.toString().padStart(3, '0')} a "${nuevoEstado}"?`);
            
            if (!confirmacion) {
                // Revertir la selección
                const select = event.target;
                select.value = estadoAnterior;
                return;
            }

            const data = {
                venta_id: ventaId,
                nuevo_estado: nuevoEstado,
                estado_anterior: estadoAnterior
            };

            fetch('../../php/actualizar_estado_venta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Estado actualizado correctamente');
                    cargarVentas(); // Recargar la tabla
                } else {
                    alert('Error: ' + data.error);
                    // Revertir la selección en caso de error
                    const select = event.target;
                    select.value = estadoAnterior;
                }
            })
            .catch(error => {
                alert('Error al actualizar estado: ' + error);
                // Revertir la selección en caso de error
                const select = event.target;
                select.value = estadoAnterior;
            });
        }

        function cancelarVenta(ventaId) {
            const confirmacion = confirm(`¿Está seguro de cancelar la venta #${ventaId.toString().padStart(3, '0')}?`);
            
            if (!confirmacion) return;

            const data = {
                venta_id: ventaId,
                nuevo_estado: 'cancelada',
                estado_anterior: 'completada' // Asumimos que se está cancelando una venta completada
            };

            fetch('../../php/actualizar_estado_venta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Venta cancelada correctamente');
                    cargarVentas();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error al cancelar venta: ' + error);
            });
        }

        function procesarVenta(event) {
            event.preventDefault();
            
            if (carrito.length === 0) {
                alert('El carrito está vacío');
                return;
            }

            const metodoPago = document.getElementById('metodoPago').value;
            
            const ventaData = {
                productos: carrito,
                metodo_pago: metodoPago
            };

            fetch('../../php/procesar_venta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(ventaData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let mensaje = 'Venta procesada con éxito';
                    if (data.estado === 'pendiente') {
                        mensaje += '. Estado: PENDIENTE - ' + (data.mensaje || 'Revisar stock de productos');
                    }
                    alert(mensaje);
                    cerrarModal();
                    cargarVentas();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error al procesar la venta: ' + error);
            });
        }

        // Funciones para ver detalles de venta
        function mostrarDetalles(ventaId) {
            fetch(`../../php/obtener_venta.php?id=${ventaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    const venta = data.venta;
                    const detalles = data.detalles;

                    document.getElementById('detailId').textContent = `#${venta.id.toString().padStart(3, '0')}`;
                    document.getElementById('detailDate').textContent = venta.fecha_venta;
                    document.getElementById('detailEmployee').textContent = venta.empleado_nombre;
                    document.getElementById('detailPayment').textContent = venta.metodo_pago;
                    
                    const detailItems = document.getElementById('detailItems');
                    detailItems.innerHTML = '';
                    
                    detalles.forEach(detalle => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${detalle.producto_nombre}</td>
                            <td>${detalle.cantidad}</td>
                            <td>CLP ${parseFloat(detalle.precio_unitario).toFixed(0)}</td>
                            <td>CLP ${parseFloat(detalle.subtotal).toFixed(0)}</td>
                        `;
                        detailItems.appendChild(row);
                    });
                    
                    document.getElementById('detailTotal').textContent = `CLP ${parseFloat(venta.total).toFixed(0)}`;
                    document.getElementById('detailsModal').style.display = 'block';
                })
                .catch(error => {
                    alert('Error al obtener los detalles: ' + error);
                });
        }

        function imprimirTicket(ventaId) {
            fetch(`../../php/obtener_venta.php?id=${ventaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    const venta = data.venta;
                    const detalles = data.detalles;

                    const ticket = `
                        ============================
                        TICKET DE VENTA
                        ============================
                        ID: #${venta.id.toString().padStart(3, '0')}
                        Fecha: ${venta.fecha_venta}
                        Empleado: ${venta.empleado_nombre}
                        Método de Pago: ${venta.metodo_pago}
                        ============================
                        ${detalles.map(d => 
                            `${d.producto_nombre} x${d.cantidad} - CLP ${parseFloat(d.subtotal).toFixed(0)}`
                        ).join('\n')}
                        ============================
                        Total: CLP ${parseFloat(venta.total).toFixed(0)}
                        ============================
                        ¡Gracias por su compra!
                    `;
                    
                    const ventanaImpresion = window.open('', '_blank');
                    ventanaImpresion.document.write(`
                        <html>
                            <head>
                                <title>Ticket de Venta</title>
                                <style>
                                    body { font-family: monospace; white-space: pre; }
                                </style>
                            </head>
                            <body>${ticket}</body>
                        </html>
                    `);
                    ventanaImpresion.document.close();
                    ventanaImpresion.print();
                })
                .catch(error => {
                    alert('Error al generar el ticket: ' + error);
                });
        }

        // Funciones de búsqueda y filtrado
        function filtrarVentas() {
            const searchText = document.getElementById('searchInput').value;
            const filterDate = document.getElementById('filterDate').value;
            const filterPayment = document.getElementById('filterPayment').value;
            const filterStatus = document.getElementById('filterStatus').value;
            
            let url = '../../php/obtener_ventas.php?';
            if (searchText) url += `busqueda=${encodeURIComponent(searchText)}&`;
            if (filterDate) url += `fecha=${encodeURIComponent(filterDate)}&`;
            if (filterPayment) url += `metodo_pago=${encodeURIComponent(filterPayment)}&`;
            if (filterStatus) url += `estado=${encodeURIComponent(filterStatus)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta de obtener_ventas.php:', data);
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    actualizarTablaVentas(data.ventas);
                    actualizarEstadisticas(data.estadisticas);
                    actualizarProductosPopulares(data.productos_populares);
                })
                .catch(error => {
                    alert('Error al filtrar ventas: ' + error);
                });
        }

        function actualizarEstadisticas(stats) {
            // Calcular ventas del día
            const ventasDia = parseFloat(stats.total_ingresos || 0);
            document.getElementById('ventasDia').textContent = `CLP ${ventasDia.toFixed(0)}`;
            document.getElementById('totalVentasDia').textContent = `${stats.total_ventas || 0} ventas`;

            // Calcular ventas del mes
            const ventasMes = parseFloat(stats.total_ingresos_mes || 0);
            document.getElementById('ventasMes').textContent = `CLP ${ventasMes.toFixed(0)}`;
            document.getElementById('totalVentasMes').textContent = `${stats.total_ventas_mes || 0} ventas`;

            // Mostrar cantidad total de productos vendidos
            const productosVendidos = parseInt(stats.total_productos_vendidos || 0);
            document.getElementById('productosVendidos').textContent = productosVendidos;
            document.getElementById('empleadosActivos').textContent = `${stats.total_empleados || 0} empleados`;

            // Calcular ticket promedio
            const ticketPromedio = parseFloat(stats.promedio_venta || 0);
            document.getElementById('ticketPromedio').textContent = `CLP ${ticketPromedio.toFixed(0)}`;

            // Mostrar horario de operación
            if (stats.primera_venta) {
                const primeraVenta = new Date(stats.primera_venta);
                document.getElementById('primeraVenta').textContent = `Primera venta: ${primeraVenta.toLocaleTimeString()}`;
            } else {
                document.getElementById('primeraVenta').textContent = 'Primera venta: --:--';
            }
            
            if (stats.ultima_venta) {
                const ultimaVenta = new Date(stats.ultima_venta);
                document.getElementById('ultimaVenta').textContent = `Última venta: ${ultimaVenta.toLocaleTimeString()}`;
            } else {
                document.getElementById('ultimaVenta').textContent = 'Última venta: --:--';
            }

            // Actualizar la tabla de ventas
            // actualizarTablaVentas(stats.ventas || []); // Esta línea se elimina para evitar que la tabla se borre
        }

        function calcularVentasMes(ventas) {
            return ventas.reduce((total, venta) => total + parseFloat(venta.total), 0);
        }

        function calcularTicketPromedio(totalIngresos, totalVentas) {
            if (totalVentas === 0) return 0;
            return totalIngresos / totalVentas;
        }

        function actualizarProductosPopulares(productos) {
            const container = document.getElementById('productosPopulares');
            container.innerHTML = '';
            
            // Ordenar productos por cantidad vendida
            const productosOrdenados = productos.sort((a, b) => b.total_vendido - a.total_vendido);
            
            productosOrdenados.forEach((producto, index) => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span class="producto-nombre">${producto.nombre}</span>
                    <span class="producto-cantidad">${producto.total_vendido} unidades</span>
                    <span class="producto-porcentaje">${calcularPorcentajeVentas(producto.total_vendido, productos)}%</span>
                `;
                container.appendChild(li);
            });
        }

        function calcularPorcentajeVentas(cantidadProducto, todosProductos) {
            const totalVentas = todosProductos.reduce((sum, p) => sum + p.total_vendido, 0);
            if (totalVentas === 0) return 0;
            return ((cantidadProducto / totalVentas) * 100).toFixed(1);
        }

        function cargarVentas() {
            fetch('../../php/obtener_ventas.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta de obtener_ventas.php:', data);
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    actualizarTablaVentas(data.ventas);
                    actualizarEstadisticas(data.estadisticas);
                    actualizarProductosPopulares(data.productos_populares);
                })
                .catch(error => {
                    console.error('Error al cargar las ventas:', error);
                });
        }

        function actualizarEstadisticasPeriodicamente() {
            setInterval(cargarVentas, 60000);
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', () => {
            cargarVentas();
            actualizarEstadisticasPeriodicamente();
            
            // Event listeners para filtros
            document.getElementById('searchInput').addEventListener('input', filtrarVentas);
            document.getElementById('filterDate').addEventListener('change', filtrarVentas);
            document.getElementById('filterPayment').addEventListener('change', filtrarVentas);
            document.getElementById('filterStatus').addEventListener('change', filtrarVentas);
        });
    </script>
</body>
</html>