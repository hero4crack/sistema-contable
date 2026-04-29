<?php
include('conecxion_bd.php');

if (isset($_GET['id_empleado'])) {

    $id = $_GET['id_empleado'];
    $sql = "DELETE FROM empleados WHERE id_empleado = '$id'";

    
    
    if ($conexion->query($sql) === TRUE) {
         echo "<script type='text/javascript'>";
            echo "alert('empleado eliminado');";
            echo "window.location.href = '../VIEWS/empleados.php';";
            echo "</script>";
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
   echo "<script type='text/javascript'>";
            echo "alert('ID no especificado');";
            echo "window.location.href = '../VIEWS/empleados.php';";
            echo "</script>";
}
?>