<?php
include_once('conecxion_bd.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    
    $sql = "UPDATE empleados
            SET 
            cedula = '".$cedula."',
            nombre_completo = '".$nombre."',
            telefono = '".$telefono."'
            WHERE id_empleado = '".$id."'
            ";

    if ($conexion->query($sql) === TRUE) {

        echo "<script type='text/javascript'>";
        echo "alert('Se edito exitosamente');";
        echo "window.location.href = '../VIEWS/empleados.php';";
        echo "</script>";
    } else {
        echo "<script type='text/javascript'>";
        echo "alert('Error en los datos');";
        echo "window.location.href = '../VIEWS/empleados.php';";
        echo "</script>";
        echo "Error al actualizar: " . $conexion->error;
    }
} else {
    echo "Método de solicitud no válido.";
}
