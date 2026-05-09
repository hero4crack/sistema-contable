<?php
require_once 'conecxion_bd.php';

// Función para listar los asientos en la tabla principal
function obtenerAsientos($conexion) {
    $sql = "SELECT a.id_asiento, a.nro_comprobante, a.fecha_asiento, a.glosa, 
                   SUM(d.debe) as total_debe, SUM(d.haber) as total_haber 
            FROM asiento_diario a 
            LEFT JOIN asiento_detalle d ON a.id_asiento = d.id_asiento 
            GROUP BY a.id_asiento 
            ORDER BY a.fecha_asiento DESC";
    return $conexion->query($sql);
}

// ESTA ES LA FUNCIÓN QUE TE FALTA PARA EL FORMULARIO
function obtenerCuentasParaAsiento($conexion) {
    $sql = "SELECT id_cuenta, codigo_cuenta, nombre_cuenta 
            FROM catalogo_cuentas 
            WHERE permite_movimiento = 1 
            ORDER BY codigo_cuenta ASC";
    return $conexion->query($sql);
}
?>