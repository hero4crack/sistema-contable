<?php
require_once 'conecxion_bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturamos los campos exactos del Catálogo
    $id = $_POST['id_cuenta'];
    $codigo = $_POST['codigo_cuenta'];
    $nombre = $_POST['nombre_cuenta'];
    $tipo = $_POST['tipo'];
    $nivel = $_POST['nivel'];
    $movimiento = $_POST['movimiento']; // Aquí recibimos el valor (1 o 0)

    // UPDATE exclusivo para la tabla de cuentas
    $sql = "UPDATE catalogo_cuentas SET 
            codigo_cuenta = ?, 
            nombre_cuenta = ?, 
            tipo_cuenta = ?, 
            nivel = ?, 
            permite_movimiento = ? 
            WHERE id_cuenta = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssiii", $codigo, $nombre, $tipo, $nivel, $movimiento, $id);

    if ($stmt->execute()) {
        header("Location: ../VIEWS/catalogo_cuenta.php?mensaje=Cuenta actualizada con éxito");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>