<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Cafetería</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Sistema de Cafetería</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message" style="color: red; background-color: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 15px;">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message" style="color: green; background-color: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 15px;">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" action="../../php/procesar_login.php" method="POST">
                <div class="form-group">
                    <label for="username">Correo Electrónico:</label>
                    <input type="email" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="login-button">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>