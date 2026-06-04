<?php
// BACKEND/consulta_proveedor.php
require_once 'conecxion_bd.php';

function obtenerListaProveedores($conexion) {
    // Listamos solo los proveedores activos
    $sql = "SELECT * FROM proveedores WHERE estado_activo = 1 ORDER BY id_proveedor DESC";
    $resultado = $conexion->query($sql);
    return $resultado;
}
?>