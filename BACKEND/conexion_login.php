<?php
include("conecxion_bd.php");
session_start();



if (isset($_GET["iniciar"])) {

    $username = $_GET["username"];
    $password = $_GET["password"];
    



    // $sql="SELECT * FROM USUARIO WHERE usuario='$id_usuario' AND contraseña='$password' AND rol=1";
    $sql = "SELECT * FROM usuarios where usuarios.username = '$username' AND usuarios.password= '$password'"; //Sirve para guardar la informacion de la consulta de la tabla (usuario) y se almacena en la variable $sql
    $resultado = mysqli_query($conexion, $sql);
    $numero_registro = mysqli_num_rows($resultado);

    $sql3 = "SELECT * FROM usuarios WHERE username = '$username'";
    $resultado3 = mysqli_query($conexion, $sql3);
    $fila3 = mysqli_fetch_assoc($resultado3);


    if ($numero_registro != 0) {

        while (($fila = mysqli_fetch_assoc($resultado)) == true) {

                

                $_SESSION['rol'] = $fila3["rol"];
                $_SESSION['usuario'] = $fila3["username"];

                 echo "<script type='text/javascript'>";
            echo "alert('Usuario ingresado con exito');";
            echo "window.location.href = '../VIEWS/inicio.php';";
            echo "</script>";
            
        }
    } else {

            echo "<script type='text/javascript'>";
            echo "alert('Usuario no reconocido');";
            echo "window.location.href = '../VIEWS/index.php';";
            echo "</script>";
    }
}
