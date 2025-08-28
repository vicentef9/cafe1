// Variables globales
let empleados = [];
let modoEdicion = false;

// Cargar empleados al iniciar
document.addEventListener('DOMContentLoaded', cargarEmpleados);

// Función para cargar empleados
async function cargarEmpleados() {
    try {
        console.log('Cargando empleados...');
        const response = await fetch('../../php/empleados/obtener_empleados.php');
        const data = await response.json();
        
        console.log('Respuesta del servidor:', data);
        
        if (data.success) {
            empleados = data.data;
            console.log('Empleados cargados:', empleados);
            actualizarTablaEmpleados();
        } else {
            console.error('Error al cargar empleados:', data.error);
            mostrarNotificacion('Error al cargar empleados: ' + data.error, 'error');
        }
    } catch (error) {
        console.error('Error en la carga de empleados:', error);
        mostrarNotificacion('Error al cargar empleados', 'error');
    }
}

// Función para actualizar la tabla de empleados
function actualizarTablaEmpleados() {
    const tbody = document.getElementById('empleadosTableBody');
    tbody.innerHTML = '';
    
    empleados.forEach(empleado => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${empleado.id}</td>
            <td>${empleado.nombre}</td>
            <td>${empleado.apellido}</td>
            <td>${empleado.email}</td>
            <td>${empleado.rol === 'admin' ? 'Administrador' : 'Empleado'}</td>
            <td><span class="status ${empleado.estado}">${empleado.estado === 'activo' ? 'Activo' : 'Inactivo'}</span></td>
            <td>
                <button class="action-button edit" onclick="editarEmpleado(${empleado.id})">Editar</button>
                <button class="action-button delete" onclick="eliminarEmpleado(${empleado.id})">Eliminar</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Mostrar el modal y bloquear scroll de fondo
function mostrarFormulario() {
    modoEdicion = false;
    document.getElementById('modalTitle').textContent = 'Agregar Empleado';
    document.getElementById('employeeForm').reset();
    document.getElementById('empleadoId').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passwordHelp').style.display = 'none';
    document.getElementById('estadoGroup').style.display = 'none';
    document.getElementById('employeeModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Cerrar el modal y restaurar scroll
function cerrarModal() {
    document.getElementById('employeeModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function(event) {
    const modal = document.getElementById('employeeModal');
    if (event.target === modal) {
        cerrarModal();
    }
}

// Función para editar empleado
async function editarEmpleado(id) {
    try {
        const response = await fetch(`../../php/empleados/obtener_empleado.php?id=${id}`);
        const data = await response.json();
        if (!data.success) {
            mostrarNotificacion('No se encontró el empleado', 'error');
            return;
        }
        const empleado = data.data;
        modoEdicion = true;
        document.getElementById('modalTitle').textContent = 'Editar Empleado';
        document.getElementById('empleadoId').value = empleado.id;
        document.getElementById('nombre').value = empleado.nombre;
        document.getElementById('apellido').value = empleado.apellido;
        document.getElementById('email').value = empleado.email;
        document.getElementById('rol').value = empleado.rol;
        document.getElementById('estado').value = empleado.estado;
        document.getElementById('password').value = '';
        document.getElementById('password').required = false;
        document.getElementById('passwordHelp').style.display = 'block';
        document.getElementById('estadoGroup').style.display = 'block';
        document.getElementById('employeeModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    } catch (error) {
        mostrarNotificacion('Error al obtener datos del empleado', 'error');
    }
}

// Función para guardar empleado
async function guardarEmpleado(event) {
    event.preventDefault();
    // Validar campos requeridos
    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const email = document.getElementById('email').value.trim();
    const rol = document.getElementById('rol').value;
    const password = document.getElementById('password').value;
    let errorMsg = '';
    if (!nombre) errorMsg = 'El nombre es obligatorio';
    else if (!apellido) errorMsg = 'El apellido es obligatorio';
    else if (!email) errorMsg = 'El email es obligatorio';
    else if (!rol) errorMsg = 'El rol es obligatorio';
    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!errorMsg && !emailRegex.test(email)) {
        errorMsg = 'Por favor ingrese un email válido';
    }
    // Validar contraseña en modo creación
    if (!modoEdicion && !password) {
        errorMsg = 'La contraseña es requerida para nuevos empleados';
    }
    if (errorMsg) {
        mostrarNotificacion(errorMsg, 'error');
        return;
    }
    const formData = {
        id: document.getElementById('empleadoId').value,
        nombre: nombre,
        apellido: apellido,
        email: email,
        rol: rol
    };
    if (modoEdicion) {
        formData.estado = document.getElementById('estado').value;
        if (password) {
            formData.password = password;
        }
    } else {
        formData.password = password;
    }
    try {
        const url = modoEdicion ? 
            '../../php/empleados/actualizar_empleado.php' : 
            '../../php/empleados/guardar_empleado.php';
        const response = await fetch(url, {
            method: modoEdicion ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        const data = await response.json();
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            cerrarModal();
            await cargarEmpleados();
        } else {
            mostrarNotificacion('Error: ' + data.error, 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error al guardar empleado: ' + error.message, 'error');
    }
}

// Función para eliminar empleado
async function eliminarEmpleado(id) {
    if (!confirm('¿Está seguro de que desea eliminar este empleado?')) {
        return;
    }

    try {
        const response = await fetch(`../../php/empleados/eliminar_empleado.php?id=${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();
        
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            cargarEmpleados();
        } else {
            mostrarNotificacion('Error: ' + data.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion('Error al eliminar empleado', 'error');
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion ${tipo}`;
    notificacion.textContent = mensaje;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 3000);
}

// Búsqueda y filtrado
document.getElementById('searchInput').addEventListener('input', filtrarEmpleados);
document.getElementById('filterRole').addEventListener('change', filtrarEmpleados);

function filtrarEmpleados() {
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const filterRole = document.getElementById('filterRole').value;
    
    const empleadosFiltrados = empleados.filter(empleado => {
        const matchesSearch = 
            empleado.nombre.toLowerCase().includes(searchText) ||
            empleado.apellido.toLowerCase().includes(searchText) ||
            empleado.email.toLowerCase().includes(searchText);
        
        const matchesRole = !filterRole || empleado.rol === filterRole;
        
        return matchesSearch && matchesRole;
    });
    
    const tbody = document.getElementById('empleadosTableBody');
    tbody.innerHTML = '';
    
    empleadosFiltrados.forEach(empleado => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${empleado.id}</td>
            <td>${empleado.nombre}</td>
            <td>${empleado.apellido}</td>
            <td>${empleado.email}</td>
            <td>${empleado.rol === 'admin' ? 'Administrador' : 'Empleado'}</td>
            <td><span class="status ${empleado.estado}">${empleado.estado === 'activo' ? 'Activo' : 'Inactivo'}</span></td>
            <td>
                <button class="action-button edit" onclick="editarEmpleado(${empleado.id})">Editar</button>
                <button class="action-button delete" onclick="eliminarEmpleado(${empleado.id})">Eliminar</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
} 