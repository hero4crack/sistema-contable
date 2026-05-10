<?php
function obtenerEstadoResultados($conexion) {
    // Buscamos saldos de cuentas de Ingresos (4), Costos (5) y Gastos (6)
    $sql = "SELECT 
                c.codigo_cuenta, 
                c.nombre_cuenta, 
                (SUM(IFNULL(m.haber, 0)) - SUM(IFNULL(m.debe, 0))) as saldo_ingreso,
                (SUM(IFNULL(m.debe, 0)) - SUM(IFNULL(m.haber, 0))) as saldo_egreso
            FROM catalogo_cuentas c
            INNER JOIN asiento_detalle m ON c.id_cuenta = m.id_cuenta
            WHERE c.codigo_cuenta LIKE '4%' OR c.codigo_cuenta LIKE '5%' OR c.codigo_cuenta LIKE '6%'
            GROUP BY c.id_cuenta, c.codigo_cuenta, c.nombre_cuenta
            ORDER BY c.codigo_cuenta ASC";
            
    return $conexion->query($sql);
}
?>