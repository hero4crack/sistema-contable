<?php
include ('conecxion_bd.php');



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['cedula'];
    $password_hash = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    


    try { 
    $sql = "INSERT INTO `empleados` (`cedula`,`nombre_completo`,`telefono`) 
            VALUES ('$username', '$password_hash', '$telefono')";

    if ($conexion->query($sql) === TRUE) {
         echo "<script type='text/javascript'>";
            echo "alert('Usuario ingresado con exito');";
            echo "window.location.href = '../VIEWS/empleados.php';";
          echo "</script>";
        exit();
  
}

} catch (mysqli_sql_exception $e ) {

            $conexion->rollback();
            
            if ($e->getCode()==1062) {               
              echo "<script type='text/javascript'>";
            echo "alert('Usuario duplicado');";
            echo "window.location.href = '../VIEWS/empleados.php';";
            echo "</script>";
        
            } else {
            throw ($e)  ;                      
                }

        }
    }

$conexion->close();
?>
