<?php
// ============================================
// 1. PRIMERO: Iniciar sesión (siempre al inicio)
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// 2. Incluir conexión a BD
// ============================================
include("conecxion_bd.php");

// ============================================
// 3. Procesar el login
// ============================================
if (isset($_GET["iniciar"])) {

    $username = $_GET["username"];
    $password = $_GET["password"];

    // Consulta para verificar usuario
    $sql = "SELECT * FROM usuarios WHERE usuarios.nombre_usuario = '$username' AND usuarios.password = '$password'";
    $resultado = mysqli_query($conexion, $sql);
    $numero_registro = mysqli_num_rows($resultado);

    // Consulta para obtener datos del usuario
    $sql3 = "SELECT * FROM usuarios WHERE nombre_usuario = '$username'";
    $resultado3 = mysqli_query($conexion, $sql3);
    $fila3 = mysqli_fetch_assoc($resultado3);

    if ($numero_registro != 0) {
        // Guardar datos en sesión
        $_SESSION['rol'] = $fila3["rol"];
        $_SESSION['usuario'] = $fila3["nombre_usuario"]; // Cambié 'username' por 'nombre_usuario'

        // Redirigir con JavaScript
        echo "<script type='text/javascript'>";
        echo "alert('Usuario ingresado con éxito');";
        echo "window.location.href = '../VIEWS/inicio.php';";
        echo "</script>";
        
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Usuario no reconocido');";
        echo "window.location.href = '../VIEWS/index.php';";
        echo "</script>";
    }
}
?>