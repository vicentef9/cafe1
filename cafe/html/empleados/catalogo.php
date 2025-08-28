<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/cartas_catalogo.css">
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
            <h1>Catálogo de Productos</h1>
            
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Buscar producto...">
                <select id="categoryFilter">
                    <option value="">Todas las categorías</option>
                    <option value="cafe">Café</option>
                    <option value="te">Té</option>
                    <option value="comidas">Comidas</option>
                    <option value="postres">Postres</option>
                    <option value="bebidas">Bebidas</option>
                    <option value="insumos">Insumos</option>
                </select>
            </div>

            <div class="card-container" id="cardContainer">
                <div class="card" data-category="cafe" data-name="café americano">
                    <div class="card-front">
                        <img src="../../img/Cafe-americano-portada.webp" alt="Café Americano" class="card-img">
                        <h3>Café Americano</h3>
                       <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Café negro tradicional, servido caliente. Ideal para comenzar el día.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
                <div class="card" data-category="cafe" data-name="capuchino">
                    <div class="card-front">
                        <img src="../../img/Cappuccino_PeB.jpg" alt="Capuchino" class="card-img">
                        <h3>Capuchino</h3>
                       <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Espuma de leche y café espresso, decorado con canela.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
                <div class="card" data-category="comidas" data-name="sandwich de jamón">
                    <div class="card-front">
                        <img src="../../img/sandwich-de-jamon-y-queso.webp" alt="Sandwich de Jamón" class="card-img">
                        <h3>Sandwich de Jamón</h3>
                      <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Pan fresco, jamón, queso y vegetales. Perfecto para el almuerzo.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
                <div class="card" data-category="postres" data-name="Tarta de Manzana">
                    <div class="card-front">
                        <img src="../../img/tarta-de-manzana.jpg" alt="Tarta de Manazana" class="card-img">
                        <h3>Tarta de Manzana</h3>
                      <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Gran Tarta de Manzana, muy sabrosa y digna de acompañar un buen café.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
                <div class="card" data-category="postres" data-name="Pie de Limon">
                    <div class="card-front">
                        <img src="../../img/pie-de-limon.jpg" alt="Pie de Limon" class="card-img">
                        <h3>Pie de Limon</h3>
                      <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Agridulce Pie de Limon, se vende en trozos, cada uno de gran sabor que no olvidarás jamás.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
                <div class="card" data-category="postres" data-name="Helado de Chocolate">
                    <div class="card-front">
                        <img src="../../img/helado-chocolate.jpg" alt="Helado de Chocolate" class="card-img">
                        <h3>Helado de Chocolate</h3>
                      <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Helado sabor chocolate, perfecto para acompañar aquellos calurosos dias de verano.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
                <div class="card" data-category="te" data-name="Té de Manzanilla">
                    <div class="card-front">
                        <img src="../../img/te-de-manzanilla.jpg" alt="Té de Manzanilla" class="card-img">
                        <h3>Té de Manzanilla</h3>
                      <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Té sabor manzanilla, para serenar cuerpo y mente.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
                <div class="card" data-category="cafe" data-name="Café Espresso">
                    <div class="card-front">
                        <img src="../../img/cafe-espresso.jpg" alt="Café Espresso" class="card-img">
                        <h3>Café Espresso</h3>
                      <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                    </div>
                    <div class="card-back">
                        <p>Rápido y de sabor concentrado, quedarás despierto como si ubieras dormido 2 dias seguidos.</p>
                        <button class="card-btn" onclick="flipCard(this)">Volver</button>
                    </div>
                </div>
            </div>

            <!-- Mensaje cuando no hay resultados -->
            <div id="noResults" class="no-results" style="display: none;">
                <p>No se encontraron productos que coincidan con los filtros seleccionados.</p>
            </div>
        </div>
    </div>

    <script>
        // Función para voltear las tarjetas
        function flipCard(btn) {
            const card = btn.closest('.card');
            card.classList.toggle('flipped');
        }

        // Función para filtrar productos
        function filtrarProductos() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            const cards = document.querySelectorAll('.card');
            const noResults = document.getElementById('noResults');
            let visibleCards = 0;

            cards.forEach(card => {
                const cardName = card.getAttribute('data-name').toLowerCase();
                const cardCategory = card.getAttribute('data-category').toLowerCase();
                
                const matchesSearch = !searchTerm || cardName.includes(searchTerm);
                const matchesCategory = !categoryFilter || cardCategory === categoryFilter;
                
                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                    visibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Mostrar mensaje si no hay resultados
            if (visibleCards === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }

        // Función para limpiar filtros
        function limpiarFiltros() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = '';
            filtrarProductos();
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Filtro por búsqueda en tiempo real
            document.getElementById('searchInput').addEventListener('input', filtrarProductos);
            
            // Filtro por categoría
            document.getElementById('categoryFilter').addEventListener('change', filtrarProductos);
            
            // Inicializar vista
            filtrarProductos();
        });

        // Función para cargar productos dinámicamente (para implementación futura)
        function cargarProductosDinamicos() {
            fetch('../../php/obtener_productos_catalogo.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('cardContainer');
                    container.innerHTML = '';
                    
                    data.forEach(producto => {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.setAttribute('data-category', producto.categoria.toLowerCase());
                        card.setAttribute('data-name', producto.nombre.toLowerCase());
                        
                        card.innerHTML = `
                            <div class="card-front">
                                <img src="${producto.imagen || '../../img/default-product.jpg'}" alt="${producto.nombre}" class="card-img">
                                <h3>${producto.nombre}</h3>
                                <button class="card-btn" onclick="flipCard(this)">Ver detalles</button>
                            </div>
                            <div class="card-back">
                                <p>${producto.descripcion || 'Sin descripción disponible'}</p>
                                <p><strong>Precio: CLP ${producto.precio}</strong></p>
                                <button class="card-btn" onclick="flipCard(this)">Volver</button>
                            </div>
                        `;
                        
                        container.appendChild(card);
                    });
                    
                    // Aplicar filtros después de cargar
                    filtrarProductos();
                })
                .catch(error => {
                    console.error('Error al cargar productos:', error);
                });
        }
    </script>
</body>
</html>
