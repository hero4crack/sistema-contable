<?php
// Primero incluimos tu archivo de conexión (verifica que el nombre sea exacto)
require_once 'conecxion_bd.php'; 

function obtenerCatalogo($conexion) {
    // Consulta para traer las cuentas ordenadas por código
    $sql = "SELECT * FROM catalogo_cuentas ORDER BY codigo_cuenta ASC";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conexion->error);
    }

    return $resultado;
}
?>