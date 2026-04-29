<?php
include('conecxion_bd.php');

if (isset($_GET['id_empresa'])) {

    $id = $_GET['id_empresa'];
    $sql = "DELETE FROM empresas_clientes WHERE id_empresa = '$id'";

    
    
    if ($conexion->query($sql) === TRUE) {
         echo "<script type='text/javascript'>";
            echo "alert('empresa eliminada');";
            echo "window.location.href = '../VIEWS/empresas_clientes.php';";
            echo "</script>";
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    echo "ID no especificado.";
}
?>