<?php
include('conecxion_bd.php');

if (isset($_GET['id_factura'])) {

    $id = $_GET['id_factura'];
    $sql = "DELETE FROM facturas WHERE id_factura = '$id'";

    
    
    if ($conexion->query($sql) === TRUE) {
         echo "<script type='text/javascript'>";
            echo "alert('factura eliminada');";
            echo "window.location.href = '../VIEWS/libro_facturas.php';";
            echo "</script>";
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    echo "ID no especificado.";
}
?>