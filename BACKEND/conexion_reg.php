<?php
include ('conecxion_login.php');



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $password_hash = $_POST['password_hash'];
    $rol = $_POST['rol'];
    


    try { 
    $sql = "INSERT INTO `usuarios` (`username`,`password`,`rol`) 
            VALUES ('$username', '$password','$rol')";

    if ($conexion->query($sql) === TRUE) {
         echo "<script type='text/javascript'>";
            echo "alert('Usuario ingresado con exito');";
            echo "window.location.href = '../VIEWS/index.php';";
          echo "</script>";
        exit();
  
}

} catch (mysqli_sql_exception $e ) {

            $conexion->rollback();
            
            if ($e->getCode()==1062) {               
              echo "<script type='text/javascript'>";
            echo "alert('Usuario duplicado');";
            echo "window.location.href = './registro.php';";
            echo "</script>";
        
            } else {
            throw ($e)  ;                      
                }

        }
    }

$conexion->close();
?>
