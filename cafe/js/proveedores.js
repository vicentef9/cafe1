document.addEventListener('DOMContentLoaded', function() {
    // Guardar proveedor por AJAX
    const form = document.getElementById('supplierForm');
    if (form) {
        form.onsubmit = guardarProveedor;
    }
    // Botón cancelar cierra el modal
    const cancelBtn = document.querySelector('#supplierForm .cancel-button');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            cerrarModal();
        });
    }
    // Cerrar modal cuando se hace clic en el botón de cerrar
    var closeBtn = document.querySelector('.close-button');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            cerrarModal();
        });
    }
    // Cerrar modal cuando se hace clic fuera del contenido
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('supplierModal');
        if (event.target === modal) {
            cerrarModal();
        }
    });
    document.getElementById('searchInput').addEventListener('input', filtrarProveedores);
    document.getElementById('filterCategory').addEventListener('change', filtrarProveedores);
    document.getElementById('filterStatus').addEventListener('change', filtrarProveedores);
    document.getElementById('filterRating').addEventListener('change', filtrarProveedores);
});

// Función para mostrar el modal
function mostrarFormulario() {
    document.getElementById('modalTitle').textContent = 'Agregar Proveedor';
    document.getElementById('supplierForm').reset();
    document.getElementById('proveedor_id').value = '';
    document.getElementById('supplierModal').style.display = 'block';
}

// Animación de cierre y mensaje visual
function cerrarModal() {
    const modal = document.getElementById('supplierModal');
    if (modal) {
        modal.classList.add('fade-out');
        setTimeout(() => {
            modal.style.display = 'none';
            modal.classList.remove('fade-out');
        }, 300); // Duración de la animación
    }
}

function mostrarMensaje(mensaje, tipo = 'success') {
    let msgDiv = document.getElementById('msgProveedor');
    if (!msgDiv) {
        msgDiv = document.createElement('div');
        msgDiv.id = 'msgProveedor';
        msgDiv.style.position = 'fixed';
        msgDiv.style.top = '20px';
        msgDiv.style.right = '20px';
        msgDiv.style.zIndex = '9999';
        msgDiv.style.padding = '15px 25px';
        msgDiv.style.borderRadius = '8px';
        msgDiv.style.fontWeight = 'bold';
        msgDiv.style.transition = 'opacity 0.3s';
        document.body.appendChild(msgDiv);
    }
    msgDiv.textContent = mensaje;
    msgDiv.style.background = tipo === 'success' ? '#4caf50' : '#f44336';
    msgDiv.style.color = '#fff';
    msgDiv.style.opacity = '1';
    msgDiv.style.display = 'block';
    setTimeout(() => {
        msgDiv.style.opacity = '0';
        setTimeout(() => { msgDiv.style.display = 'none'; }, 300);
    }, 2000);
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('supplierModal').style.display = 'none';
}

// Función para editar un proveedor
function editarProveedor(id) {
    document.getElementById('modalTitle').innerText = "Editar Proveedor";
    fetch(`../../php/obtener_proveedor.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const proveedor = data.proveedor || data; // Soporta ambos formatos
            document.getElementById('supplierModal').style.display = 'block';
            document.getElementById('proveedor_id').value = proveedor.id || '';
            document.getElementById('nombre').value = proveedor.nombre || '';
            document.getElementById('contacto').value = proveedor.contacto || '';
            document.getElementById('telefono').value = proveedor.telefono || '';
            document.getElementById('email').value = proveedor.email || '';
            document.getElementById('categoria').value = proveedor.categoria || '';
            document.getElementById('direccion').value = proveedor.direccion || '';
            document.getElementById('calificacion').value = proveedor.calificacion || '0';
            document.getElementById('estado').value = proveedor.estado || 'activo';
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al cargar los datos del proveedor', 'error');
        });
}

// Función para eliminar un proveedor
function eliminarProveedor(id) {
    if (confirm('¿Está seguro de que desea eliminar este proveedor?')) {
        fetch(`../../php/eliminar_proveedor.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensaje('Proveedor eliminado correctamente', 'success');
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                mostrarMensaje(data.error || 'Error al eliminar el proveedor', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al eliminar el proveedor', 'error');
        });
    }
}

// Función para filtrar proveedores
function filtrarProveedores() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('filterCategory').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const ratingFilter = document.getElementById('filterRating').value;
    const rows = document.querySelectorAll('.suppliers-table tbody tr');

    rows.forEach(row => {
        const nombre = row.cells[1].textContent.toLowerCase();
        const categoria = row.cells[5].textContent.toLowerCase();
        const estado = row.cells[7].textContent.toLowerCase();
        const calificacion = row.querySelectorAll('.star.filled').length;

        const matchesSearch = nombre.includes(searchTerm);
        const matchesCategory = !categoryFilter || categoria === categoryFilter.toLowerCase();
        const matchesStatus = !statusFilter || estado === statusFilter.toLowerCase();
        const matchesRating = !ratingFilter || calificacion >= parseInt(ratingFilter);

        row.style.display = matchesSearch && matchesCategory && matchesStatus && matchesRating ? '' : 'none';
    });
}

// Cerrar el modal si se hace clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('supplierModal');
    if (event.target == modal) {
        cerrarModal();
    }
}

// Función para guardar el proveedor
function guardarProveedor(event) {
    if (event) event.preventDefault();
    const form = document.getElementById('supplierForm');
    if (!form) return false;
    const formData = new FormData(form);
    fetch('../../php/guardar_proveedor.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            alert('Respuesta inválida del servidor:\n' + text);
            data = { success: false, error: 'Respuesta inválida del servidor' };
        }
        if (data.success) {
            mostrarMensaje(data.message || 'Proveedor guardado correctamente', 'success');
            cerrarModal();
            setTimeout(() => { location.reload(); }, 1000);
        } else {
            mostrarMensaje(data.error || 'Error al guardar el proveedor', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al guardar el proveedor', 'error');
    });
    return false;
}