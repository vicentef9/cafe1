<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            // Buscar el usuario en la base de datos
            $stmt = $conn->prepare("SELECT id, nombre, apellido, email, password, rol FROM empleados WHERE email = ? AND estado = 'activo'");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // Depuración: usuario encontrado
                $_SESSION['debug'] = 'Usuario encontrado. Verificando contraseña...';
                if (password_verify($password, $usuario['password'])) {
                    // Iniciar sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['nombre_usuario'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
                    $_SESSION['rol'] = $usuario['rol'];
                    unset($_SESSION['debug']);
                    // Redirigir según el rol
                    if ($usuario['rol'] === 'administrador') {
                        header("Location: ../html/empleados/admin_usuarios.php");
                    } else {
                        header("Location: ../html/empleados/productos.php");
                    }
                    exit();
                } else {
                    // Depuración: contraseña incorrecta
                    $_SESSION['debug'] = 'Contraseña incorrecta. Hash en BD: ' . $usuario['password'];
                    $_SESSION['error'] = "Credenciales inválidas o usuario inactivo";
                    header("Location: ../html/autenticacion/login.php");
                    exit();
                }
            } else {
                // Depuración: usuario no encontrado
                $_SESSION['debug'] = 'Usuario no encontrado o inactivo.';
                $_SESSION['error'] = "Credenciales inválidas o usuario inactivo";
                header("Location: ../html/autenticacion/login.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al procesar el login: " . $e->getMessage();
            header("Location: ../html/autenticacion/login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Por favor, complete todos los campos";
        header("Location: ../html/autenticacion/login.php");
        exit();
    }
} else {
    header("Location: ../html/autenticacion/login.php");
    exit();
}
?>