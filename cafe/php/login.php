<?php
require_once 'session.php';

// Si el usuario ya está logueado, redirigir a index.php
if (isLoggedIn()) {
    header("Location: ../vistas/index.php");
    exit();
}

// Configuración de la base de datos
$servidor = "localhost";
$usuario_db = "root";
$clave_db = "";
$nombre_bd = "comicverse";

$error = "";

//--------------PROCESAMIENTO DEL FORMULARIO------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtiene y limpia los datos del formulario
    $usuario = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $contraseña = isset($_POST["password"]) ? $_POST["password"] : "";

    //--------------------VALIDACION DE CAMPOS-------------------
    if (empty($usuario) || empty($contraseña)) {
        $error = "Por favor complete todos los campos.";
    } else {
        //---------------------------CONEXION A LA BASE DE DATOS-----------------------
        try {
            $conexion = new mysqli($servidor, $usuario_db, $clave_db, $nombre_bd);

            if ($conexion->connect_error) {
                throw new Exception("Error de conexión: " . $conexion->connect_error);
            }

            $conexion->set_charset("utf8");

            //---------------------------BUSQUEDA DEL USUARIO------------------------------------------
            $stmt = $conexion->prepare("SELECT id, name, password FROM users WHERE name = ?");
            
            if ($stmt) {
                $stmt->bind_param("s", $usuario);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows === 1) {
                    $row = $resultado->fetch_assoc();
                    // Verificar la contraseña hasheada
                    if (password_verify($contraseña, $row["password"])) {
                        // Establecer variables de sesión
                        $_SESSION["user_id"] = $row["id"];
                        $_SESSION["username"] = $row["name"];
                        $_SESSION["last_activity"] = time();
                        
                        // Redirigir a la página principal
                        header("Location: ../vistas/index.php");
                        exit();
                    } else {
                        $error = "Contraseña incorrecta.";
                    }
                } else {
                    $error = "Usuario no encontrado.";
                }

                $stmt->close();
            } else {
                $error = "Error en la consulta: " . $conexion->error;
            }
            $conexion->close();

        } catch (Exception $e) {
            $error = "Error en el servidor: " . $e->getMessage();
        }
    }

    if (!empty($error)) {
        $_SESSION["error_mensaje"] = $error;
        header("Location: login.php");
        exit();
    }
}
?>