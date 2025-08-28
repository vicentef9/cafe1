
// Cargar usuarios al iniciar la página
document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();
    // Eventos para filtros
    document.getElementById('searchInput').addEventListener('input', filtrarUsuarios);
    document.getElementById('filterRole').addEventListener('change', filtrarUsuarios);
    document.getElementById('filterStatus').addEventListener('change', filtrarUsuarios);
});
document.getElementById('userForm').addEventListener('submit', function(event) {
    event.preventDefault();
    guardarUsuario();
});

function mostrarFormulario() {
    document.getElementById('modalTitle').textContent = 'Agregar Usuario';
    document.getElementById('userForm').reset();
    document.getElementById('usuario_id').value = ''; // Limpiar ID para nuevo usuario
    document.getElementById('password').setAttribute('required', 'required'); // Contraseña requerida al crear
    document.getElementById('userModal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('userModal').style.display = 'none';
}


// Función para cargar la lista de usuarios (sin filtros)
async function cargarUsuarios() {
    await filtrarUsuarios();
}

// Función para filtrar usuarios según los inputs
async function filtrarUsuarios() {
    try {
        const busqueda = document.getElementById('searchInput').value.trim();
        const rol = document.getElementById('filterRole').value;
        const estado = document.getElementById('filterStatus').value;

        const params = new URLSearchParams();
        params.append('accion', 'listar');
        if (busqueda) params.append('busqueda', busqueda);
        if (rol) params.append('rol', rol);
        if (estado) params.append('estado', estado);

        const response = await fetch(`../../php/usuarios.php?${params.toString()}`);
        const rawText = await response.text();
        //console.log('Raw response:', rawText);
        let result;
        try {
            result = JSON.parse(rawText);
        } catch (e) {
            console.error('JSON parse error:', e);
            console.log('Response that failed to parse:', rawText);
            throw new Error('Invalid JSON response from server');
        }

        if (!result.success) {
            throw new Error(result.message || 'Error desconocido');
        }

        const empleadosTableBody = document.getElementById('empleadosTableBody');
        empleadosTableBody.innerHTML = '';

        result.data.forEach(usuario => {
            const estadoClass = usuario.estado === 'activo' ? 'activo' : 'inactivo';
            const rowClass = usuario.estado === 'inactivo' ? 'usuario-inactivo' : '';
            empleadosTableBody.innerHTML += `
                <tr class="${rowClass}">
                    <td>${usuario.id}</td>
                    <td>${usuario.nombre}</td>
                    <td>${usuario.apellido}</td>
                    <td>${usuario.email}</td>
                    <td>${usuario.rol}</td>
                    <td><span class="status ${estadoClass}">${usuario.estado}</span></td>
                    <td>
                        <button class="action-button edit" onclick="editarUsuario(${usuario.id})">Editar</button>
                        <button class="action-button delete" onclick="eliminarUsuario(${usuario.id})">Eliminar</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error al filtrar usuarios:', error);
        alert('Error al filtrar la lista de usuarios: ' + error.message);
    }
}

// Función para guardar o actualizar un usuario
async function guardarUsuario() {
    try {
        const form = document.getElementById('userForm');
        const formData = new FormData(form);
        const usuarioId = document.getElementById('usuario_id').value;

        const url = `../../php/usuarios.php?accion=${usuarioId ? 'actualizar' : 'crear'}`;
        
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (!result.success && !result.exito) {
            throw new Error(result.message || result.mensaje || 'Error al guardar usuario');
        }

        alert(usuarioId ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente');
        cerrarModal();
        form.reset();
        await cargarUsuarios();
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar usuario: ' + error.message);
    }
}

// Función para cargar datos de usuario en el modal de edición
async function editarUsuario(id) {
    try {
        const response = await fetch(`../../php/usuarios.php?accion=obtener&id=${id}`);
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Error al obtener usuario');
        }

        const usuario = result.data;
        
        document.getElementById('modalTitle').textContent = 'Editar Usuario';
        document.getElementById('usuario_id').value = usuario.id;
        document.getElementById('nombre').value = usuario.nombre;
        document.getElementById('apellido').value = usuario.apellido;
        document.getElementById('email').value = usuario.email;
        document.getElementById('password').removeAttribute('required'); // Contraseña opcional al editar
        document.getElementById('rol').value = usuario.rol === 'admin' ? 'administrador' : usuario.rol;
        document.getElementById('estado').value = usuario.estado;
        
        document.getElementById('userModal').style.display = 'block';
    } catch (error) {
        console.error('Error al cargar datos del usuario:', error);
        alert('Error al cargar los datos del usuario: ' + error.message);
    }
}

// Función para eliminar un usuario
async function eliminarUsuario(id) {
    if (!confirm('¿Está seguro de que desea eliminar este usuario?')) {
        return;
    }
    
    try {
        // Usar GET para eliminar, ya que el backend espera GET
        const response = await fetch(`../../php/usuarios.php?accion=eliminar&id=${id}`);
        const resultado = await response.json();
        
        if (resultado.success || resultado.exito) {
            alert('Usuario eliminado exitosamente');
            cargarUsuarios();
        } else {
            // Mostrar mensaje exacto del backend
            alert('Error al eliminar usuario: ' + (resultado.message || resultado.mensaje || JSON.stringify(resultado)));
        }
    } catch (error) {
        console.error('Error al eliminar usuario:', error);
        alert('Error al eliminar el usuario');
    }
}

window.filtrarUsuarios = filtrarUsuarios;