// Función para mostrar el modal
function mostrarFormulario() {
    document.getElementById('modalTitle').textContent = 'Agregar Producto';
    // Resetear el formulario completamente
    document.getElementById('productForm').reset();
    document.getElementById('producto_id').value = '';
    
    // Limpiar todos los campos explícitamente
    document.getElementById('nombre').value = '';
    document.getElementById('categoria').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('fecha_vencimiento').value = '';
    
    // Remover clases de error si existen
    limpiarErroresCampos();
    
    document.getElementById('productModal').style.display = 'block';
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('productModal').style.display = 'none';
    // Limpiar errores al cerrar
    limpiarErroresCampos();
}

// Función para limpiar campos con errores
function limpiarErroresCampos() {
    var campos = ['nombre', 'categoria', 'descripcion', 'fecha_vencimiento'];
    campos.forEach(function(campo) {
        var elemento = document.getElementById(campo);
        if (elemento) {
            elemento.classList.remove('error');
        }
    });
}

// Función para editar un producto
function editarProducto(id) {
    document.getElementById('modalTitle').innerText = "Editar Producto";
    
    // Limpiar errores antes de cargar datos
    limpiarErroresCampos();
    
    fetch(`../../php/obtener_producto.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Mostrar el modal antes de llenar los campos
            document.getElementById('productModal').style.display = 'block';
            
            // Llenar los campos con validación de datos
            document.getElementById('producto_id').value = data.id || '';
            document.getElementById('nombre').value = data.nombre || '';
            document.getElementById('categoria').value = data.categoria || '';
            document.getElementById('descripcion').value = data.descripcion || '';
            document.getElementById('fecha_vencimiento').value = data.fecha_vencimiento || '';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del producto');
        });
}

// Función para eliminar un producto
function eliminarProducto(id) {
    console.log('ID a eliminar:', id); // <-- Depuración
    if (confirm('¿Está seguro de que desea eliminar este producto del inventario?')) {
        fetch('../../php/eliminar_producto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Producto eliminado correctamente');
                location.reload();
            } else {
                let msg = data.error || 'Error al eliminar el producto';
                if (data.debug_post) {
                    msg += '\nDebug POST: ' + JSON.stringify(data.debug_post);
                }
                alert(msg);
            }
        })
        .catch(error => {
            console.error('Error en fetch eliminarProducto:', error); // <-- Depuración
            alert('Error al eliminar el producto');
        });
    }
}

// Función para filtrar productos
function filtrarProductos() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('filterCategory').value;
    const rows = document.querySelectorAll('.products-table tbody tr');

    rows.forEach(row => {
        const nombre = row.cells[1].textContent.toLowerCase();
        const categoria = row.cells[2].textContent.toLowerCase();
        
        const matchesSearch = nombre.includes(searchTerm);
        const matchesCategory = !categoryFilter || categoria === categoryFilter.toLowerCase();

        row.style.display = matchesSearch && matchesCategory ? '' : 'none';
    });
}

// Cerrar el modal si se hace clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        cerrarModal();
    }
}

// Eventos
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal cuando se hace clic en el botón de cerrar
    const closeButton = document.querySelector('.close-button');
    if (closeButton) {
        closeButton.addEventListener('click', cerrarModal);
    }

    // Filtrar productos
    const searchInput = document.getElementById('searchInput');
    const filterCategory = document.getElementById('filterCategory');
    
    if (searchInput) {
        searchInput.addEventListener('input', filtrarProductos);
    }
    if (filterCategory) {
        filterCategory.addEventListener('change', filtrarProductos);
    }
    
    // Agregar validación en tiempo real para campos del formulario
    var campos = ['nombre', 'categoria', 'descripcion', 'fecha_vencimiento'];
    campos.forEach(validarCampoEnTiempoReal);
});

// Función para validación en tiempo real
function validarCampoEnTiempoReal(nombreCampo) {
    var campo = document.getElementById(nombreCampo);
    if (campo) {
        campo.addEventListener('input', function() {
            campo.classList.remove('error');
        });
        campo.addEventListener('change', function() {
            campo.classList.remove('error');
        });
    }
}

// Función para guardar el producto
function guardarProducto(event) {
    event.preventDefault();
    
    // Primero validar el formulario
    if (!validarFormularioProducto()) {
        return false;
    }
    
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    
    // Si el campo producto_id está vacío o es '0', eliminarlo del FormData para que no se envíe
    const productoId = formData.get('producto_id');
    if (!productoId || productoId === '0') {
        formData.delete('producto_id');
    }
    
    fetch('../../php/guardar_producto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Producto guardado correctamente');
            cerrarModal();
            location.reload();
        } else {
            alert(data.error || 'Error al guardar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar el producto');
    });
    
    return false;
}

// Función auxiliar para validación (movida desde el archivo PHP)
function validarFormularioProducto() {
    // Limpiar errores previos
    limpiarErroresCampos();
    
    // Obtener valores de los campos
    var nombre = document.getElementById('nombre').value.trim();
    var categoria = document.getElementById('categoria').value;
    var descripcion = document.getElementById('descripcion').value.trim();
    var fechaVencimiento = document.getElementById('fecha_vencimiento').value;
    
    var errores = [];
    var camposConError = [];
    
    // Validación de nombre (solo letras, espacios y algunos caracteres especiales)
    var soloLetrasEspacios = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s&.-]+$/;
    if (!nombre) {
        errores.push('El nombre del producto es obligatorio.');
        camposConError.push('nombre');
    } else if (nombre.length < 2) {
        errores.push('El nombre debe tener al menos 2 caracteres.');
        camposConError.push('nombre');
    } else if (!soloLetrasEspacios.test(nombre)) {
        errores.push('El nombre solo puede contener letras, espacios y los caracteres &, ., -');
        camposConError.push('nombre');
    }
    
    // Validación de categoría
    if (!categoria) {
        errores.push('Debe seleccionar una categoría.');
        camposConError.push('categoria');
    }
    
    // Validación de descripción (opcional pero si se ingresa debe ser válida)
    if (descripcion && descripcion.length < 10) {
        errores.push('La descripción debe tener al menos 10 caracteres o dejarse vacía.');
        camposConError.push('descripcion');
    }
    
    // Validación de fecha de vencimiento
    if (!fechaVencimiento) {
        errores.push('La fecha de vencimiento es obligatoria.');
        camposConError.push('fecha_vencimiento');
    } else {
        var fechaIngresada = new Date(fechaVencimiento);
        var fechaActual = new Date();
        fechaActual.setHours(0, 0, 0, 0); // Resetear horas para comparar solo fechas
        
        if (fechaIngresada <= fechaActual) {
            errores.push('La fecha de vencimiento debe ser posterior a la fecha actual.');
            camposConError.push('fecha_vencimiento');
        }
        
        // Verificar que no sea una fecha muy lejana (más de 5 años)
        var fechaMaxima = new Date();
        fechaMaxima.setFullYear(fechaMaxima.getFullYear() + 5);
        if (fechaIngresada > fechaMaxima) {
            errores.push('La fecha de vencimiento no puede ser superior a 5 años desde hoy.');
            camposConError.push('fecha_vencimiento');
        }
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
    var confirmacion = confirm('¿Está seguro de que desea guardar este producto con la información ingresada?');
    if (!confirmacion) {
        return false;
    }
    
    return true;
}

// Función para marcar campo con error
function marcarCampoConError(nombreCampo) {
    var campo = document.getElementById(nombreCampo);
    if (campo) {
        campo.classList.add('error');
    }
}