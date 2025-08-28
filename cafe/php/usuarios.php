<?php
// Set headers first
header('Content-Type: application/json');

// Start session before any output
session_start();

// Include database connection
require_once 'conexion.php';

// Clear any output buffers
ob_clean();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$accion = $_GET['accion'] ?? '';

// Función para validar fortaleza de contraseña
function validarContrasenaFuerte($password) {
    $errores = [];
    
    // Verificar longitud mínima de 8 caracteres
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres';
    }
    
    // Verificar que tenga al menos una mayúscula
    if (!preg_match('/[A-Z]/', $password)) {
        $errores[] = 'La contraseña debe contener al menos una letra mayúscula';
    }
    
    // Verificar que tenga al menos una minúscula
    if (!preg_match('/[a-z]/', $password)) {
        $errores[] = 'La contraseña debe contener al menos una letra minúscula';
    }
    
    // Verificar que tenga al menos un número
    if (!preg_match('/[0-9]/', $password)) {
        $errores[] = 'La contraseña debe contener al menos un número';
    }
    
    // Opcional: Verificar que tenga al menos un carácter especial
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]/', $password)) {
        $errores[] = 'La contraseña debe contener al menos un carácter especial (!@#$%^&*()_+-=[]{}|;:,.<>?)';
    }
    
    return $errores;
}

// Función para validar datos de usuario
function validarUsuario($datos) {
    global $accion;
    $errores = [];
    
    if (empty($datos['nombre'])) {
        $errores[] = 'El nombre es requerido';
    } elseif (strlen($datos['nombre']) < 2) {
        $errores[] = 'El nombre debe tener al menos 2 caracteres';
    }
    
    if (empty($datos['apellido'])) {
        $errores[] = 'El apellido es requerido';
    } elseif (strlen($datos['apellido']) < 2) {
        $errores[] = 'El apellido debe tener al menos 2 caracteres';
    }
    
    if (empty($datos['email'])) {
        $errores[] = 'El email es requerido';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El email no es válido';
    }
    
    // Validación de contraseña para crear nuevo usuario
    if ($accion === 'crear') {
        if (empty($datos['password'])) {
            $errores[] = 'La contraseña es requerida';
        } else {
            // Validar fortaleza de la contraseña
            $erroresPassword = validarContrasenaFuerte($datos['password']);
            $errores = array_merge($errores, $erroresPassword);
        }
    }
    
    // Validación de contraseña para actualizar usuario (solo si se proporciona)
    if ($accion === 'actualizar' && !empty($datos['password'])) {
        $erroresPassword = validarContrasenaFuerte($datos['password']);
        $errores = array_merge($errores, $erroresPassword);
    }
    
    if (empty($datos['rol'])) {
        $errores[] = 'El rol es requerido';
    } elseif (!in_array($datos['rol'], ['administrador', 'empleado'])) {
        $errores[] = 'El rol no es válido';
    }
    
    return $errores;
}

// Manejar las diferentes acciones
switch ($accion) {
    case 'listar':
        try {
            // Filtros opcionales
            $filtros = [];
            $params = [];
            if (!empty($_GET['busqueda'])) {
                $filtros[] = "(nombre LIKE :busqueda OR apellido LIKE :busqueda OR email LIKE :busqueda)";
                $params[':busqueda'] = '%' . $_GET['busqueda'] . '%';
            }
            if (!empty($_GET['rol'])) {
                $filtros[] = "rol = :rol";
                $params[':rol'] = $_GET['rol'];
            }
            if (!empty($_GET['estado'])) {
                $filtros[] = "estado = :estado";
                $params[':estado'] = $_GET['estado'];
            }
            $sql = "SELECT id, nombre, apellido, email, rol, estado FROM empleados";
            if ($filtros) {
                $sql .= " WHERE " . implode(' AND ', $filtros);
            }
            $sql .= " ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true,
                'data' => $usuarios
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al listar usuarios: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'obtener':
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }

            $stmt = $conn->prepare("SELECT id, nombre, apellido, email, rol, estado FROM empleados WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }

            echo json_encode([
                'success' => true,
                'data' => $usuario
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'crear':
        $datos = $_POST;
        $errores = validarUsuario($datos);
        
        if (!empty($errores)) {
            http_response_code(400);
            echo json_encode([
                'exito' => false, 
                'mensaje' => 'Errores de validación: ' . implode('; ', $errores)
            ]);
            exit;
        }
        
        try {
            // Verificar si el email ya existe
            $stmt = $conn->prepare("SELECT id FROM empleados WHERE email = ?");
            $stmt->execute([$datos['email']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['exito' => false, 'mensaje' => 'El email ya está registrado']);
                exit;
            }
            
            // Crear nuevo usuario
            $stmt = $conn->prepare("INSERT INTO empleados (nombre, apellido, email, password, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $datos['nombre'],
                $datos['apellido'],
                $datos['email'],
                password_hash($datos['password'], PASSWORD_DEFAULT),
                $datos['rol']
            ]);
            
            echo json_encode(['exito' => true, 'mensaje' => 'Usuario creado exitosamente']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['exito' => false, 'mensaje' => 'Error al crear usuario: ' . $e->getMessage()]);
        }
        break;
        
    case 'actualizar':
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception('ID no proporcionado');
            }

            $datos = [
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'rol' => $_POST['rol'],
                'estado' => $_POST['estado'],
                'id' => $id
            ];
            
            // Solo agregar password a la validación si se proporciona
            if (!empty($_POST['password'])) {
                $datos['password'] = $_POST['password'];
            }

            $errores = validarUsuario($datos);
            if (!empty($errores)) {
                throw new Exception('Errores de validación: ' . implode('; ', $errores));
            }

            $sql = "UPDATE empleados SET 
                    nombre = :nombre,
                    apellido = :apellido,
                    email = :email,
                    rol = :rol,
                    estado = :estado";

            // Agregar actualización de contraseña solo si se proporciona una nueva
            if (!empty($_POST['password'])) {
                $sql .= ", password = :password";
                $datos['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->execute($datos);

            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'eliminar':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['exito' => false, 'mensaje' => 'ID no proporcionado']);
            exit;
        }
        
        try {
            $stmt = $conn->prepare("DELETE FROM empleados WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['exito' => true, 'mensaje' => 'Usuario eliminado exitosamente']);
            } else {
                http_response_code(404);
                echo json_encode(['exito' => false, 'mensaje' => 'Usuario no encontrado']);
            }
        } catch (PDOException $e) {
            // Si es error de clave foránea, desactivar usuario
            if ($e->getCode() == '23000') {
                $stmt = $conn->prepare("UPDATE empleados SET estado = 'inactivo' WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                echo json_encode(['exito' => true, 'mensaje' => 'El usuario tiene registros asociados y fue desactivado (estado inactivo) en vez de eliminado.']);
            } else {
                http_response_code(500);
                echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar usuario: ' . $e->getMessage()]);
            }
        }
        break;
        
    case 'validar_password':
        // Endpoint especial para validar contraseña en tiempo real
        $password = $_POST['password'] ?? '';
        $errores = validarContrasenaFuerte($password);
        
        if (empty($errores)) {
            echo json_encode([
                'valida' => true,
                'mensaje' => 'Contraseña fuerte',
                'nivel' => 'fuerte'
            ]);
        } else {
            echo json_encode([
                'valida' => false,
                'errores' => $errores,
                'nivel' => 'debil'
            ]);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'Acción no válida']);
        break;
}
?>