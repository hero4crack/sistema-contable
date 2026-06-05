<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturamos los campos exactos del Catálogo
    $id = $_POST['id'];
  
    $nombre = $_POST['cuenta'];
    $tipo = $_POST['tipo'];
    $nivel = $_POST['nivel'];
    $movimiento = $_POST['movimiento']; // Aquí recibimos el valor (1 o 0)

    // UPDATE exclusivo para la tabla de cuentas
    $sql = "UPDATE catalogo_cuentas SET 
            nombre_cuenta = '$nombre', 
            tipo_cuenta = '$tipo', 
            nivel = '$nivel', 
            permite_movimiento = '$movimiento' 
            WHERE id_cuenta = '$id'";

    if ($conexion->query($sql)) {
        header("Location: ../VIEWS/catalogo_cuenta.php?mensaje=Cuenta actualizada con éxito");
    } else {
         echo "Error al actualizar: " . $conexion->error;
    }
    
}
?>