<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte Técnico</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/styles-soporte.css">
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

        <div class="main-content">
            <h1>Preguntas Frecuentes</h1>

            <div class="support-container">
                <div class="faq-section">
                    <h2>¿Tienes dudas? Encuentra aquí las respuestas más comunes</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Cómo inicio sesión en el sistema?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            Para iniciar sesión, dirígete a la página de login e ingresa tu usuario y contraseña proporcionados por el administrador. Si olvidas tu contraseña, contacta al administrador del sistema.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Cómo agregar nuevos productos al catálogo?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            Ve a la sección "Productos" en el menú lateral, haz clic en "Agregar Producto", completa los campos obligatorios como nombre, precio, categoría y descripción. No olvides guardar los cambios.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Cómo gestionar el inventario?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            En la sección "Inventario" puedes ver el stock actual de todos los productos. Para reabastecer, selecciona el producto y agrega la cantidad deseada. El sistema actualizará automáticamente las existencias.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Cómo registrar una venta?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            Ve a la sección "Ventas", selecciona los productos que el cliente desea comprar, especifica las cantidades y el sistema calculará automáticamente el total. Confirma la venta para actualizar el inventario.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Cómo agregar o editar proveedores?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            En la sección "Proveedores" puedes agregar nuevos proveedores completando el formulario con nombre, contacto, teléfono y email. También puedes editar o eliminar proveedores existentes desde esta misma sección.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Qué hacer si el sistema no responde?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            Primero, verifica tu conexión a internet. Si el problema persiste, cierra el navegador y vuelve a abrir el sistema. Si aún tienes problemas, contacta al administrador del sistema.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Cómo ver el historial de ventas?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            En la sección "Ventas" encontrarás un historial completo de todas las transacciones realizadas. Puedes filtrar por fecha, cliente o estado de la venta para encontrar información específica.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Puedo cambiar mi contraseña?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            Actualmente el cambio de contraseña debe ser realizado por el administrador del sistema. Contacta al administrador para solicitar un cambio de contraseña.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Qué hago si un producto no aparece en el catálogo?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            Verifica que el producto esté registrado en la sección "Productos" y que tenga stock disponible en "Inventario". Si el producto existe pero no aparece, puede ser un problema temporal del sistema.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            ¿Cómo cerrar sesión correctamente?
                            <span>+</span>
                        </div>
                        <div class="faq-answer">
                            Para cerrar sesión de forma segura, busca el botón de "Cerrar Sesión" o "Logout" en la interfaz. Nunca cierres simplemente el navegador sin cerrar sesión primero.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAnswer(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('span');
            
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                icon.textContent = '+';
            } else {
                answer.style.display = 'block';
                icon.textContent = '-';
            }
        }
    </script>
</body>
</html>
